<?php
declare(strict_types=1);

namespace Sura\Libs;

use Sura\Contracts\TimeZonaInterface;

abstract class TimeZona implements TimeZonaInterface
{
    /**
     * @var array|string[]
     */
    private static array $time_zone = array(
        0 => 'Europe/Moscow',
        1 => 'Europe/Kiev',
        2 => 'Pacific/Samoa',
        3 => 'US/Hawaii',
        4 => 'US/Alaska',
        5 => 'America/Los_Angeles',
        6 => 'America/Denver',
        7 => 'America/Chicago',
        8 => 'America/New_York',
        9 => 'America/Caracas',
        10 => 'America/Buenos_Aires',
        11 => 'America/Sao_Paulo',
        12 => 'Atlantic/Azores',
        13 => 'Europe/London',
        14 => 'Europe/Berlin',
        15 => 'Europe/Kiev',
        16 => 'Europe/Moscow',
        17 => 'Asia/Yerevan',
        18 => 'Asia/Yekaterinburg',
        19 => 'Asia/Novosibirsk',
        20 => 'Asia/Krasnoyarsk',
        21 => 'Asia/Singapore',
        22 => 'Asia/Tokyo',
        23 => 'Asia/Vladivostok',
        24 => 'Australia/Sydney',
        25 => 'Asia/Kamchatka',
    );

    /**
     * Set timezone
     *
     * @param $id
     * @return bool
     */
    public static function time_zone(int $id)  : bool
    {
        return date_default_timezone_set(self::$time_zone[$id]);
    }

    /**
     * Language list
     *
     * @return string
     */
    public static function list(): string
    {
        $row = '';
        $time_zone = self::$time_zone;
        foreach ($time_zone as $key => $value){
            $row.= '<option value="'.$key.'">'.$value.'</option>'.printf($row);
        }
        return $row;
    }
}