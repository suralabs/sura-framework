<?php


namespace Sura\Cache;


use Sura\Cache\Contracts\CacheItemInterface;
use Sura\Cache\Contracts\CacheItemPoolInterface;
use Sura\Cache\Exeption\CacheExeption;

class Cache implements CacheItemInterface
{

    /**
     * Cache driver
     *
     * @var CacheItemPoolInterface
     */
    private $pool;

    public function __construct(CacheItemPoolInterface $pool)
    {
        $this->pool = $pool;

        if (!$pool instanceof AdapterInterface) {
            return;
        }

    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return true;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        try {
            $item = $this->pool->getItem($key);
        } catch (CacheException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->isHit($key) ? $this->pool->getItem($key) : $default;
    }

    /**
     * @param $key
     * @return bool
     */
    public function isHit($key)
    {
        return $this->pool->hasItem($key);
    }

    /**
     * @param $key
     * @param $value
     * @param null $ttl
     * @return mixed
     */
    public function set($key, $value, $ttl = null)
    {
        try {
            //$f = [];
            //$item = ($key, $value);
            $item = array($key => $value);

            //$item = $this->pool->getItem($key)->set($value);
        } catch (CacheException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
//        if (null !== $ttl) {
//            $item->expiresAfter($ttl);
//        }

        return $this->pool->save($item);
    }

    /**
     * @param $expiration
     * @return mixed
     */
    public function expiresAt($expiration)
    {
        return true;
    }

    /**
     * @param $time
     * @return mixed
     */
    public function expiresAfter($time)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function delete($key)
    {
        try {
            return $this->pool->deleteItem($key);
        } catch (CacheException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function clear()
    {
        return $this->pool->clear();
    }
}