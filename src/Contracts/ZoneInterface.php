<?php


namespace Sura\Contracts;


interface ZoneInterface
{

    /**
     * Set timezone
     *
     * @param $id
     * @return bool
     */
    public static function zone(int $id): bool;

}