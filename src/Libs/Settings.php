<?php


namespace Sura\Libs;


class Settings
{
 public static function loadsettings()
 {
     return include __DIR__.'/../../../../../config/config.php';
 }
}