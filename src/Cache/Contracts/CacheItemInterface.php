<?php


namespace Sura\Cache\Contracts;


interface CacheItemInterface
{
    public function getKey();
    public function get($key, $default = null);
    public function isHit($key);
    public function set($key, $value, $ttl = null);
    public function expiresAt($expiration);
    public function expiresAfter($time);
}