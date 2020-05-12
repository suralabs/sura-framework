<?php


namespace Sura\Libs;


class Settings
{
    /**
     * load settings
     * @return array
     */
    public static function loadsettings()
 {
     return include __DIR__.'/../../../../../config/config.php';
 }
}