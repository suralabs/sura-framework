<?php

namespace Sura\Libs;

class Profile_check
{
    /**
     * @param $id
     * @return bool
     */
    public static function timezona($id)
    {
        $timezona = array(
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

        return date_default_timezone_set($timezona[$id]);

    }
}