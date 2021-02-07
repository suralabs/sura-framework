<?php


namespace Sura\Contracts;


use Sura\Libs\Registry;

interface RegistryInterface
{

    /**
     * Возвращает данные по ключу или null, если не данных нет
     *
     * @param string $name
     * @return string|bool|null
     */
    public static function get(mixed $name): string|bool|null|array;

    /**
     * Проверяет существуют ли данные по ключу
     *
     * @param string $name
     * @return bool
     */
    public static function exists(string $name): bool;

    /**
     * Сохраняет данные по ключу в статическом хранилище
     *
     * @param string $name
     * @param mixed $obj
     * @return string
     */
    public static function set(mixed $name, mixed $obj): mixed;
}