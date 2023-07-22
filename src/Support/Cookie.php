<?php

/*
 * Copyright (c) 2023 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\Support;

class Cookie
{
    /**
     * remove cookie
     * @param string $name
     * @return void
     */
    public static function remove(string $name): void
    {
        $domain = $_SERVER['HTTP_HOST'];
        $expires = time() + 100;
        setcookie($name, '', $expires, '/', $domain, true, true);
    }

    /**
     * add to cookie
     * @param string $name
     * @param string $value
     * @param false|int $expires
     * @return void
     */
    public static function append(string $name, string $value, false|int $expires): void
    {
        $domain = $_SERVER['HTTP_HOST'];
        if ($expires > 0) {
            $expires = time() + ($expires * 86400);
        } else {
            $expires = 0;
        }
        setcookie($name, $value, $expires, '/', $domain, true, true);
    }

    /**
     * get value cookie
     * @param string $name
     * @return string
     */
    public static function get(string $name): string
    {
        return $_COOKIE[$name] ?? '';
    }
}
