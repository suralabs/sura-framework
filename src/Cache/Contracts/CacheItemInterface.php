<?php

namespace Sura\Cache\Contracts;

interface CacheItemInterface
{
    /**
     * @return mixed
     */
    public function getKey();

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param $key
     * @return mixed
     */
    public function isHit($key);

    /**
     * @param $key
     * @param $value
     * @param null $ttl
     * @return mixed
     */
    public function set($key, $value, $ttl = null);

    /**
     * @param $expiration
     * @return mixed
     */
    public function expiresAt($expiration);

    /**
     * @param $time
     * @return mixed
     */
    public function expiresAfter($time);
}