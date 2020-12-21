<?php
/**
 * Статический класс registry
 */
namespace Sura\Libs;

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
    protected function __clone() {}
 
    /**
     * Проверяет существуют ли данные по ключу
     *
     * @param string $name
     * @return bool
     */
    public static function exists($name) : bool
    {
        return isset(self::$store[$name]);
    }
 
    /**
     * Возвращает данные по ключу или null, если не данных нет
     *
     * @param string $name
     * @return unknown
     */
    public static function get($name) : string | array|null
    {
        return (isset(self::$store[$name])) ? self::$store[$name] : null;
    }
 
    /**
     * Сохраняет данные по ключу в статическом хранилище
     *
     * @param string $name
     * @param unknown $obj
     * @return unknown
     */
    public static function set($name, $obj) 
    {
        return self::$store[$name] = $obj;
    }
}
