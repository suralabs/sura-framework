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
    public static function getLangDate(): array;

    /**
     * @return array
     */
    public static function getLangs(): array;

    /**
     * Check language
     *
     * @return string
     */
    public static function checkLang(): string;

    /**
     *
     */
    public static function setLocale(): void;

    /**
     * @return array Languages list
     */
    public static function langList(): array;
}