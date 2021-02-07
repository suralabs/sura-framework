<?php


namespace Sura\Contracts;


use JetBrains\PhpStorm\NoReturn;
use Sura\Libs\Langs;
use Sura\Libs\Request;
use Sura\Libs\Tools;

interface LangsInterface
{

    /**
     * @return array
     */
    public static function get_langdate(): array;

    /**
     * @return array
     */
    public static function get_langs(): array;

    /**
     * Check language
     *
     * @return string
     */
    public static function check_lang(): string;

    /**
     *
     */
    public static function setlocale(): void;

    /**
     * @return array Languages list
     */
    public static function lang_list(): array;
}