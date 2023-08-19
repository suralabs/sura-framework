<?php

/*
 * Copyright (c) 2023 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\Filesystem;

use Sura\Contracts\Filesystem\FilesystemInterface;
use function floor;
use function sprintf;
use function strlen;

/**
 *
 */
class Filesystem implements FilesystemInterface
{
    /**
     * Create dir
     * @param string $dir
     * @param int $mode
     * @return bool
     */
    public static function createDir(string $dir, int $mode = 0777): bool
    {
        return !(!is_dir($dir) && !mkdir($dir, $mode, true) && !is_dir($dir));
    }

    /**
     * Delete file OR directory
     * @param string $file
     * @return bool
     */
    public static function delete(string $file): bool
    {
        if (is_dir($file)) {
            if (!str_ends_with($file, '/')) {
                $file .= '/';
            }
            $files = glob($file . '*', GLOB_MARK);
            foreach ((array)$files as $file_) {
                if (is_string($file_)) {
                    self::delete($file_);
                }
            }
            rmdir($file);
            return true;
        }
        if (is_file($file)) {
            unlink($file);
            return true;
        }
        return false;
    }

    /**
     * Ceck file or dir
     * @param string $file
     * @return bool
     */
    public static function check(string $file): bool
    {
        return is_file($file) || is_dir($file);
    }

    /**
     * @param string $from
     * @param string $to
     * @return bool
     */
    public static function copy(string $from, string $to): bool
    {
        if (is_file($from) && !is_file($to)) {
            return copy($from, $to);
        }
        return false;
    }

    /**
     * size dir
     * @param string $directory
     * @return bool|int
     */
    public static function dirSize(string $directory): bool|int
    {
        if (!is_dir($directory)) {
            return -1;
        }
        $size = 0;
        if ($DIR = opendir($directory)) {
            while (($dir_file = readdir($DIR)) !== false) {
                if (is_link($directory . '/' . $dir_file) || $dir_file === '.' || $dir_file === '..') {
                    continue;
                }
                if (is_file($directory . '/' . $dir_file)) {
                    $size += filesize($directory . '/' . $dir_file);
                } elseif (is_dir($directory . '/' . $dir_file)) {
                    $dirSize = self::dirSize($directory . '/' . $dir_file);
                        $size += $dirSize;
                }
            }
            closedir($DIR);
        }
        return $size;
    }

    /**
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    public static function humanFileSize(int $bytes, int $decimals = 1): string
    {
        $sizes = 'BKMGTP';
        $factor = (int) floor(( strlen((string)$bytes) - 1 ) / 3);
        $unit = $sizes[$factor] ?? '';
        return sprintf("%.{$decimals}f", $bytes / (1000 ** $factor)) .
            ( $unit === 'B' ? $unit : $unit . 'B' );
    }
}
