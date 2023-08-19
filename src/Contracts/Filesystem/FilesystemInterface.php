<?php

/*
 * Copyright (c) 2023 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\Contracts\Filesystem;

/**
 *
 */
interface FilesystemInterface
{
    /**
     * @param string $dir
     * @param int $mode
     * @return bool
     */
    public static function createDir(string $dir, int $mode = 0777): bool;

    /**
     * @param string $file
     * @return bool
     */
    public static function delete(string $file): bool;

    /**
     * @param string $file
     * @return bool
     */
    public static function check(string $file): bool;

    /**
     * @param string $from
     * @param string $to
     * @return bool
     */
    public static function copy(string $from, string $to): bool;

    /**
     * @param string $directory
     * @return bool|int
     */
    public static function dirSize(string $directory): bool|int;

    /**
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    public static function humanFileSize(int $bytes, int $decimals = 1): string;
}
