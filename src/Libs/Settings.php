<?php


namespace Sura\Libs;


class Settings
{
    /**
     * load settings
     * @return array
     */
    public static function loadsettings() : array
 {
     //TODO update
     return self::load();
 }

    /**
     * load settings
     * @return array
     */
    public static function load() : array
    {
        return include __DIR__.'/../../../../../config/config.php';
    }
}