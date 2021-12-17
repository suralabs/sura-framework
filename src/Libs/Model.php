<?php
declare(strict_types=1);

namespace Sura\Libs;

use Sura\Database\Connection;

abstract class Model
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

			self::$database = new Connection($dsn, $user, $password);
		}
		return self::$database;
	}
}