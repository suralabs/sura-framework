<?php

/*
 * Copyright (c) 2023 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\Contracts\Security;

/**
 *
 */
interface Factory
{
    /**
     * @param string $act
     * @return int
     */
    public static function limit(string $act): int;

    /**
     * @param string $act
     * @param false|string $text
     * @return bool
     */
    public static function check(string $act, false|string $text = false): bool;

    /**
     * @param string $act
     * @param bool|string $text
     * @return void
     */
    public static function logInsert(string $act, bool|string $text = false): void;
}
