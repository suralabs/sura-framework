<?php

/*
 * Copyright (c) 2022 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\Contracts\Security;

interface Factory
{
    public static function limit(string $act): int;
    public static function check(string $act, false|string $text = false): bool;
    public static function logInsert(string $act, bool|string $text = false): void;
}
