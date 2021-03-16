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

    public static function getDB(): Connection
    {
        if (self::$database == null) {
            $config = Settings::load();

            $dsn = 'mysql:host=' . $config['dbhost'] . ';dbname=' . $config['dbname'];
            $user = $config['dbuser'];
            $password = $config['dbpass'];
            self::$database = new Connection($dsn, $user, $password);
        }
        return self::$database;
    }

    /**
     * @return string|array|null
     */
    public function user_info(): string|array|null
    {
        return Registry::get('user_info');
    }

    /**
     * @return bool
     */
    public function logged(): bool|null
    {
        return Registry::get('logged');
    }

    /**
     * @return \Sura\Libs\Db|null
     * @deprecated
     */
    public function db(): null|Db
    {
        return Db::getDB();
    }

    /**
     * @return array
     */
    public function get_langs(): array
    {
        return Langs::get_langs();
    }
}
