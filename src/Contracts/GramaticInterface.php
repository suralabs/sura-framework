<?php

declare(strict_types=1);

namespace Sura\Contracts;

/**
 *
 */
interface GramaticInterface
{
    /**
     * @param string $name
     * @param string $declination
     * @return mixed
     */
    public static function DeclName(string $name, string $declination): mixed;

    /**
     * @param string $var
     * @param bool $lower
     * @param bool $punkt
     * @return mixed
     */
    public static function totranslit(string $var, bool $lower = true, bool $punkt = true): mixed;

    /**
     * @param int $number
     * @param array $titles
     * @return mixed
     */
    public static function declOfNum(int $number, array $titles): mixed;

    /**
     * @param string $name
     * @return mixed
     */
    public static function gramatikName(string $name): mixed;

    /**
     * @param int $num
     * @param $a
     * @param $b
     * @param $c
     * @param bool $t
     * @return mixed
     */
    public static function newGram(int $num, $a, $b, $c, bool $t = false): mixed;

}