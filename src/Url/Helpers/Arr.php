<?php

namespace Sura\Url\Helpers;

/**
 * Class Arr
 * @package Sura\Url\Helpers
 */
class Arr
{
    /**
     * @param array $items
     * @param callable $callback
     * @return array
     */
    public static function map(array $items, callable $callback)
    {
        $keys = array_keys($items);

        $items = array_map($callback, $items, $keys);

        return array_combine($keys, $items);
    }

    /**
     * @param array $items
     * @param callable $callback
     * @return mixed
     */
    public static function mapToAssoc(array $items, callable $callback)
    {
        return array_reduce($items, function (array $assoc, $item) use ($callback) {
            [$key, $value] = $callback($item);
            $assoc[$key] = $value;

            return $assoc;
        }, []);
    }
}
