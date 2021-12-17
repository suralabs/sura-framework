<?php
declare(strict_types=1);

namespace Sura\Libs;

use Sura\Contracts\ModuleInterface;
use Sura\Database\Connection;

/**
 *  Module
 *
 */
class Module implements ModuleInterface
{
    private static ?Connection $database = null;

    /**
     * Model constructor.
     *
     * Получение экземпляра класса.
     * Если он уже существует, то возвращается, если его не было,
     * то создаётся и возвращается (паттерн Singleton)
     */
    public function __construct()
    {
        self::$database = self::getDB();
    }

    /**
     * @return Connection
     */
    public static function getDB(): Connection
    {
        if (self::$database == null) {
            $config = Settings::load();

            $dsn = 'mysql:host=' . $config['dbhost'] . ';dbname=' . $config['dbname'];
            $user = $config['dbuser'];
            $password = $config['dbpass'];

//            $database = new \Sura\Database\Connection($dsn, $user, $password); // the same arguments as uses PDO

            self::$database = new Connection($dsn, $user, $password);
        }
        return self::$database;
    }

    /**
     * @return string|array|null
     */
    public function userInfo($params = 'none'): mixed
    {
        if ($params == 'none')
            return Registry::get('user_info');
        else {
            $user_info = Registry::get('user_info');
            return $user_info[$params];
        }
    }

    /**
     * @return bool
     */
    public function logged(): bool|null
    {
        return Registry::get('logged');
    }

    /**
     * @return array
     */
    public function getLangs(): array
    {
        return Langs::getLangs();
    }
}
