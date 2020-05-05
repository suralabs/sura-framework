<?php


namespace Sura\Cache\Adapter;


use Sura\Cache\Contracts\CacheItemInterface;
use Sura\Cache\Contracts\CacheItemPoolInterface;

class FileAdapter extends AbstractAdapter implements CacheItemPoolInterface
{

    /**
     * @param $key
     * @return mixed
     */
    public function getItem($key)
    {
        return true;
    }

    /**
     * @param array $keys
     * @return mixed
     */
    public function getItems(array $keys = array())
    {
        return true;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function hasItem($key)
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function clear()
    {
        return true;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function deleteItem($key)
    {
        return true;
    }

    /**
     * @param array $keys
     * @return mixed
     */
    public function deleteItems(array $keys)
    {
        return true;
    }

    /**
     * @param CacheItemInterface $item
     * @return mixed
     */
    public function save(CacheItemInterface $item)
    {
        return true;
    }

    /**
     * @param CacheItemInterface $item
     * @return mixed
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function commit()
    {
        return true;
    }
}