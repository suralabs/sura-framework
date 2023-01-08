<?php

/*
 * Copyright (c) 2023 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\tests\Filesystem;

use Sura\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

final class FilesystemTest extends TestCase
{
    public function testCreateDir(): void
    {
        $dir = __DIR__;
        $instance = Filesystem::createDir($dir . '/test/');
        self::assertEquals(true, $instance);
        $instance = Filesystem::createDir($dir . '/');
        self::assertEquals(true, $instance);
        $instance = Filesystem::createDir($dir . '/test/');
        self::assertEquals(true, $instance);
        $instance = Filesystem::createDir($dir . '/test/test/');
        self::assertEquals(true, $instance);
    }

    public function testCheck(): void
    {
        $dir = __DIR__;
        $instance = Filesystem::check($dir . '/test/');
        self::assertEquals(true, $instance);
        $instance = Filesystem::check($dir . '/fail/');
        self::assertEquals(false, $instance);
    }

    public function testCopy(): void
    {
        $dir = __DIR__;
        file_put_contents($dir . "/test/qwerty.php", 'qwerty');
        if (Filesystem::check($dir . '/test/qwerty2.php')) {
            Filesystem::delete($dir . '/test/qwerty2.php');
        }
        $instance = Filesystem::copy($dir . "/test/qwerty.php", $dir . "/test/qwerty2.php");
        self::assertEquals(true, $instance);
        $instance = Filesystem::copy($dir . "/test/qwerty.php", $dir . "/test/qwerty2.php");
        self::assertEquals(false, $instance);
    }

    public function testDirSize(): void
    {
        $dir = __DIR__;
        $instance = Filesystem::dirSize($dir . '/test/');
        self::assertEquals(12, $instance);
        $instance = Filesystem::dirSize($dir . '/test/test/');
        self::assertEquals(0, $instance);
        $instance = Filesystem::dirSize('/qwerty/');
        self::assertEquals(-1, $instance);
    }

    public function testDelete(): void
    {
        $dir = __DIR__;
        $instance = Filesystem::delete($dir . '/test/qwerty2.php');
        self::assertEquals(true, $instance);
        $instance = Filesystem::delete($dir . '/test/');
        self::assertEquals(true, $instance);
        $instance = Filesystem::delete($dir . '/test2/');
        self::assertEquals(false, $instance);
    }

    public function testFileSize(): void
    {
        $bytes = Filesystem::humanFileSize(5945766364);
        self::assertEquals('5.9GB', $bytes);
    }
}
