<?php


namespace Sura\Cache\Traits;


abstract class AbstractAdapterTrait
{
    private $createCacheItem;

    /**
     * @var \Closure needs to be set by class, signature is function(array <deferred>, string <namespace>, array <&expiredIds>)
     */
    private $mergeByLifetime;

    private $namespace;
    private $namespaceVersion = '';
    private $versioningIsEnabled = false;
    private $deferred = [];
    private $ids = [];

    /**
     * @var int|null The maximum length to enforce for identifiers or null when no limit applies
     */
    protected $maxIdLength;

    abstract protected function doFetch(array $ids);

    abstract protected function doHave(string $id);

    abstract protected function doClear(string $namespace);

    abstract protected function doDelete(array $ids);

    abstract protected function doSave(array $values, int $lifetime);

    public function hasItem($key)
    {
        $id = $this->getId($key);

        if (isset($this->deferred[$key])) {
            $this->commit();
        }

        try {
            return $this->doHave($id);
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to check if key "{key}" is cached: '.$e->getMessage(), ['key' => $key, 'exception' => $e, 'cache-adapter' => get_debug_type($this)]);

            return false;
        }
    }

    public function clear(string $prefix = '')
    {
        $this->deferred = [];
        if ($cleared = $this->versioningIsEnabled) {
            if ('' === $namespaceVersionToClear = $this->namespaceVersion) {
                foreach ($this->doFetch([static::NS_SEPARATOR.$this->namespace]) as $v) {
                    $namespaceVersionToClear = $v;
                }
            }
            $namespaceToClear = $this->namespace.$namespaceVersionToClear;
            $namespaceVersion = substr_replace(base64_encode(pack('V', mt_rand())), static::NS_SEPARATOR, 5);
            try {
                $cleared = $this->doSave([static::NS_SEPARATOR.$this->namespace => $namespaceVersion], 0);
            } catch (\Exception $e) {
                $cleared = false;
            }
            if ($cleared = true === $cleared || [] === $cleared) {
                $this->namespaceVersion = $namespaceVersion;
                $this->ids = [];
            }
        } else {
            $namespaceToClear = $this->namespace.$prefix;
        }

        try {
            return $this->doClear($namespaceToClear) || $cleared;
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to clear the cache: '.$e->getMessage(), ['exception' => $e, 'cache-adapter' => get_debug_type($this)]);

            return false;
        }
    }

    public function deleteItem($key)
    {
        return $this->deleteItems([$key]);
    }

    public function deleteItems(array $keys)
    {
        $ids = [];

        foreach ($keys as $key) {
            $ids[$key] = $this->getId($key);
            unset($this->deferred[$key]);
        }

        try {
            if ($this->doDelete($ids)) {
                return true;
            }
        } catch (\Exception $e) {
        }

        $ok = true;

        // When bulk-delete failed, retry each item individually
        foreach ($ids as $key => $id) {
            try {
                $e = null;
                if ($this->doDelete([$id])) {
                    continue;
                }
            } catch (\Exception $e) {
            }
            $message = 'Failed to delete key "{key}"'.($e instanceof \Exception ? ': '.$e->getMessage() : '.');
            CacheItem::log($this->logger, $message, ['key' => $key, 'exception' => $e, 'cache-adapter' => get_debug_type($this)]);
            $ok = false;
        }

        return $ok;
    }

    public function getItem($key)
    {
        if ($this->deferred) {
            $this->commit();
        }
        $id = $this->getId($key);

        $f = $this->createCacheItem;
        $isHit = false;
        $value = null;

        try {
            foreach ($this->doFetch([$id]) as $value) {
                $isHit = true;
            }

            return $f($key, $value, $isHit);
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to fetch key "{key}": '.$e->getMessage(), ['key' => $key, 'exception' => $e, 'cache-adapter' => get_debug_type($this)]);
        }

        return $f($key, null, false);
    }

    public function getItems(array $keys = [])
    {
        if ($this->deferred) {
            $this->commit();
        }
        $ids = [];

        foreach ($keys as $key) {
            $ids[] = $this->getId($key);
        }
        try {
            $items = $this->doFetch($ids);
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to fetch items: '.$e->getMessage(), ['keys' => $keys, 'exception' => $e, 'cache-adapter' => get_debug_type($this)]);
            $items = [];
        }
        $ids = array_combine($ids, $keys);

        return $this->generateItems($items, $ids);
    }

    public function save(CacheItemInterface $item)
    {
        if (!$item instanceof CacheItem) {
            return false;
        }
        $this->deferred[$item->getKey()] = $item;

        return $this->commit();
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        if (!$item instanceof CacheItem) {
            return false;
        }
        $this->deferred[$item->getKey()] = $item;

        return true;
    }

    public function enableVersioning($enable = true)
    {
        $wasEnabled = $this->versioningIsEnabled;
        $this->versioningIsEnabled = (bool) $enable;
        $this->namespaceVersion = '';
        $this->ids = [];

        return $wasEnabled;
    }

    public function reset()
    {
        if ($this->deferred) {
            $this->commit();
        }
        $this->namespaceVersion = '';
        $this->ids = [];
    }

    public function __sleep()
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    public function __wakeup()
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }

    public function __destruct()
    {
        if ($this->deferred) {
            $this->commit();
        }
    }

    private function generateItems(iterable $items, array &$keys): iterable
    {
        $f = $this->createCacheItem;

        try {
            foreach ($items as $id => $value) {
                if (!isset($keys[$id])) {
                    $id = key($keys);
                }
                $key = $keys[$id];
                unset($keys[$id]);
                yield $key => $f($key, $value, true);
            }
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to fetch items: '.$e->getMessage(), ['keys' => array_values($keys), 'exception' => $e, 'cache-adapter' => get_debug_type($this)]);
        }

        foreach ($keys as $key) {
            yield $key => $f($key, null, false);
        }
    }

    private function getId($key)
    {
        if ($this->versioningIsEnabled && '' === $this->namespaceVersion) {
            $this->ids = [];
            $this->namespaceVersion = '1'.static::NS_SEPARATOR;
            try {
                foreach ($this->doFetch([static::NS_SEPARATOR.$this->namespace]) as $v) {
                    $this->namespaceVersion = $v;
                }
                if ('1'.static::NS_SEPARATOR === $this->namespaceVersion) {
                    $this->namespaceVersion = substr_replace(base64_encode(pack('V', time())), static::NS_SEPARATOR, 5);
                    $this->doSave([static::NS_SEPARATOR.$this->namespace => $this->namespaceVersion], 0);
                }
            } catch (\Exception $e) {
            }
        }

        if (\is_string($key) && isset($this->ids[$key])) {
            return $this->namespace.$this->namespaceVersion.$this->ids[$key];
        }
        CacheItem::validateKey($key);
        $this->ids[$key] = $key;

        if (null === $this->maxIdLength) {
            return $this->namespace.$this->namespaceVersion.$key;
        }
        if (\strlen($id = $this->namespace.$this->namespaceVersion.$key) > $this->maxIdLength) {
            // Use MD5 to favor speed over security, which is not an issue here
            $this->ids[$key] = $id = substr_replace(base64_encode(hash('md5', $key, true)), static::NS_SEPARATOR, -(\strlen($this->namespaceVersion) + 2));
            $id = $this->namespace.$this->namespaceVersion.$id;
        }

        return $id;
    }

    /**
     * @internal
     */
    public static function handleUnserializeCallback($class)
    {
        throw new \DomainException('Class not found: '.$class);
    }

}