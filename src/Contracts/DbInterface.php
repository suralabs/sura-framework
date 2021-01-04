<?php

namespace Sura\Contracts;


/**
 * Class Db
 * @package Sura\Libs
 */
interface DbInterface
{
    /**
     * @param $db_user
     * @param $db_pass
     * @param $db_name
     * @param string $db_location
     * @param int $show_error
     * @return bool
     */
    function connect($db_user, $db_pass, $db_name, $db_location = 'localhost', $show_error = 1): bool;

    /**
     * @param $query
     * @param bool $show_error
     * @return \mysqli_result
     */
    function query($query, $show_error = true);

    /**
     * @param string $query_id
     * @return array|string[]|null
     */
    function get_row($query_id = ''): array|string|null;

    /**
     * @param string $query_id
     * @return array|null
     */
    function get_array($query_id = ''): array|null;

    /**
     * @param $query
     * @param bool $multi
     * @param bool $cache_prefix
     * @param bool $system_cache
     * @return array|mixed|string[]
     */
    function super_query($query, $multi = false): array;

    /**
     * @param string $query_id
     * @return int
     */
    function num_rows($query_id = '');

    /**
     * @return int|string
     */
    function insert_id(): int;

    /**
     * @param string $query_id
     * @return mixed
     */
    function get_result_fields($query_id = ''): array;

    /**
     * @param $source
     * @return string
     */
    function safesql($source): string;

    /**
     * @param string $query_id
     */
    function free($query_id = '');

    /**
     * close bd
     */
    function close(): bool;

    /**
     * @return float
     */
    function get_real_time(): float;

    /**
     * @param $error
     * @param $error_num
     * @param string $query
     */
    function display_error($error, $error_num, $query = '');
}