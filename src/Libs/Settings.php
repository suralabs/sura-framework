<?php
declare(strict_types=1);

namespace Sura\Libs;

use Sura\Contracts\SettingsInterface;
use function Sura\resolve;

class Settings implements SettingsInterface
{
    /**
     * load settings
     * @return array
     */
    public static function load() : array
    {
        return require resolve('app')->get('path.config').DIRECTORY_SEPARATOR.'config.php';
    }

    /**
     * @param string $parameter
     * @return string
     */
    public static function get(string $parameter): string
    {
       $config = require resolve('app')->get('path.config').DIRECTORY_SEPARATOR.'config.php';
       return $config[$parameter];
    }
}