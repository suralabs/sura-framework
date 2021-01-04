<?php

namespace Sura\Libs;

class Profile_check
{
    /**
     * @param $id
     * @return string
     */
    public static function time_zone(int $id)  : string
    {
        return TimeZona::time_zone($id);
    }

    /**
     * @return string
     */
    public static function list(): string
    {
        return TimeZona::list();
    }
}