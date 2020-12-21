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
     return include __DIR__.'/../../../../../config/config.php';
 }
}