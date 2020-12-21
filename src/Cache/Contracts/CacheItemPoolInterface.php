<?php

namespace Sura\Cache\Contracts;

interface CacheItemPoolInterface
{
    /**
     * @param $key
     * @return mixed
     */
    public function getItem($key);

    /**
     * @param array $keys
     * @return mixed
     */
    public function getItems(array $keys = array());

    /**
     * @param $key
     * @return mixed
     */
    public function hasItem($key);

    /**
     * @return mixed
     */
    public function clear();

    /**
     * @param $key
     * @return mixed
     */
    public function deleteItem($key);

    /**
     * @param array $keys
     * @return mixed
     */
    public function deleteItems(array $keys);

    /**
     * @param $item
     * @return mixed
     */
    public function save($item);

    /**
     * @param CacheItemInterface $item
     * @return mixed
     */
    public function saveDeferred(CacheItemInterface $item);

    /**
     * @return mixed
     */
    public function commit();
}