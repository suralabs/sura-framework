<?php
declare(strict_types=1);

namespace Sura\Libs;


abstract class Model
{
	private static ?\Sura\Database\Connection $database = null;
	
	/**
	 * Model constructor.
	 *
	 * Получение экземпляра класса.
	 * Если он уже существует, то возвращается, если его не было,
	 * то создаётся и возвращается (паттерн Singleton)
	 */
	public function __construct()
	{


//        $this->$database = self::getDB();
	}
	
	public static function getDB(): \Sura\Database\Connection
	{
		if (self::$database == null) {
			$config = Settings::load();
			
			$dsn = 'mysql:host=' . $config['dbhost'] . ';dbname=' . $config['dbname'];
			$user = $config['dbuser'];
			$password = $config['dbpass'];

//            $database = new \Sura\Database\Connection($dsn, $user, $password); // the same arguments as uses PDO
			
			self::$database = new \Sura\Database\Connection($dsn, $user, $password);
		}
		return self::$database;
	}
}