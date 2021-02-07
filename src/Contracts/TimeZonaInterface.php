<?php


namespace Sura\Contracts;


use Sura\Libs\TimeZona;

interface TimeZonaInterface
{

    /**
     * Set timezone
     *
     * @param $id
     * @return bool
     */
    public static function time_zone(int $id): bool;

    /**
     * Language list
     *
     * @return string
     */
    public static function list(): string;
}