<?php


namespace Sura\Cache;


use Sura\Cache\Contracts\CacheItemInterface;

class Cache implements CacheItemInterface
{

    /**
     * @return mixed
     */
    public function getKey()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function isHit()
    {
        return true;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function set($value)
    {
        return true;
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
}