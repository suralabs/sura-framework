<?php
declare(strict_types=1);

/**
 * Статический класс registry
 */
namespace Sura\Libs;

/**
 * Class Registry
 * @package Sura\Libs
 */
class Registry
{
    /**
     * Статическое хранилище для данных
     */
    protected static array $store = array();
 
    /**
     * Защита от создания экземпляров статического класса
     */
    protected function __construct() {}

    /**
     *
     */
    protected function __clone() {}

    /**
     * Проверяет существуют ли данные по ключу
     *
     * @param string $name
     * @return bool
     */
    public static function exists(string $name) : bool
    {
        return isset(self::$store[$name]);
    }

    /**
     * Возвращает данные по ключу или null, если не данных нет
     *
     * @param string $name
     * @return string|bool|null
     */
    public static function get(mixed $name) : string|bool|null|array
    {
        return (isset(self::$store[$name])) ? self::$store[$name] : null;
    }

    /**
     * Сохраняет данные по ключу в статическом хранилище
     *
     * @param string $name
     * @param mixed $obj
     * @return string
     */
    public static function set(mixed $name, mixed $obj): mixed
    {
        return self::$store[$name] = $obj;
    }
}
