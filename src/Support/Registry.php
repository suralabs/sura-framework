<?php

/*
 * Copyright (c) 2022 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\Support;

/**
 * Registry
 */
class Registry
{
    /** @var array|string[] $store Статическое хранилище для данных*/
    protected static array $store = [];

    /** Защита от создания экземпляров статического класса */
    protected function __construct()
    {
    }

    /**  */
    protected function __clone()
    {
    }

    /**
     * Проверяет существуют ли данные по ключу
     *
     * @param string $name
     * @return bool
     */
    public static function exists(string $name): bool
    {
        return isset(self::$store[$name]);
    }

    /**
     * Возвращает данные по ключу или null, если не данных нет
     *
     * @param string $name
     * @return mixed
     */
    public static function get(mixed $name): mixed
    {
        return self::$store[$name] ?? null;
    }

    /**
     * Сохраняет данные по ключу в статическом хранилище
     *
     * @param string $name
     * @param mixed $obj
     * @return mixed
     */
    public static function set(string $name, mixed $obj): mixed
    {
        return self::$store[$name] = $obj;
    }
}
