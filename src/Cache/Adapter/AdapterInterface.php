<?php


namespace Sura\Cache\Adapter;

use Sura\Cache\Contracts\CacheItemPoolInterface;

/**
 * Interface for adapters managing instances of Symfony's CacheItem.
 *
 */
interface AdapterInterface extends CacheItemPoolInterface
{
    /**
     *
     */
    public function getItem($key);

    /**
     *
     */
    public function getItems(array $keys = []);

    /**
     *
     * @return bool
     */
    public function clear(string $prefix = '');
}