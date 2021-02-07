<?php
declare(strict_types=1);
namespace Sura\Libs;

class Profile_check
{
    /**
     * @param $id
     * @return bool
     */
    public static function time_zone(int $id)  : bool
    {
        return TimeZona::time_zone($id);
    }

    /** @return string */
    public static function list(): string
    {
        return TimeZona::list();
    }
}