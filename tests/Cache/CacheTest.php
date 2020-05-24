<?php

namespace Cache;

use Sura\Cache\Adapter\FileAdapter;
use Sura\Cache\Cache;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{

    public function testExpiresAt()
    {

    }

    public function testClear()
    {

    }

    public function testGet()
    {

    }

    public function testSet()
    {

    }

    public function testExpiresAfter()
    {

    }

    public function testGetKey()
    {

    }

    public function testIsHit()
    {

    }

    public function test__construct()
    {

    }

    public function testDelete()
    {

    }

    public function testCacheFile()
    {
        $Cache = new FileAdapter();
        $Cache->dir($value = __DIR__.'/tmp/');
        $Cache = new Cache($Cache);

        $value = $instance = "foo";
        $key = 'test';

        $Cache->set($key, $value, null);
        $instance = $item = $Cache->get($key, $default = null);
        foreach ($item as $key2 => $value2) {

            $this->assertEquals($value, $value2);
        }

        return $instance;
    }
}
