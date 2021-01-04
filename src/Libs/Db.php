<?php

namespace Sura\Libs;

use Sura\Contracts\DbInterface;
use \Sura\Log\Log;

/**
 * Class Db
 * @package Sura\Libs
 */
final class Db implements DbInterface
{

    /**
     * @var null
     */
    private static $db = null; // Единственный экземпляр класса, чтобы не создавать множество подключений

    /**
     * Db constructor.
     * @param bool|object $db_id
     * @param int $query_num
     * @param string $mysql_error
     * @param string $mysql_version
     * @param int $mysql_error_num
     * @param int $MySQL_time_taken
     * @param bool|string|object $query_id
     * @param array $db_config
     */
    public function __construct(
        public bool|object $db_id = false,
        public int $query_num = 0,
        public string $mysql_error = '',
        public string $mysql_version = '',
        public int $mysql_error_num = 0,
        public int $MySQL_time_taken = 0,
        public bool|string|object $query_id = false,
        public $db_config = array(),
    )
    {
        return $this->db_config = Settings::loadsettings();
    }

    /**
     *
     */
    public function __destruct()
    {
//        if ($this->mysqli) $this->mysqli->close();
        return $this->close();

    }

    /**
     * Получение экземпляра класса. Если он уже существует, то возвращается, если его не было, то создаётся и возвращается (паттерн Singleton)
     * @return Db|null
     */
    public static function getDB() : null|Db
    {
        if (self::$db == null) {
            self::$db = new Db();
        }
        return self::$db;
    }

    /**
     * @param $db_user
     * @param $db_pass
     * @param $db_name
     * @param string $db_location
     * @param int $show_error
     * @return bool
     */
    function connect($db_user, $db_pass, $db_name, $db_location = 'localhost', $show_error = 1) : bool
    {
        $db_location = explode(":", $db_location);
        if (isset($db_location[1])) {
            $this->db_id = mysqli_connect($db_location[0], $db_user, $db_pass, $db_name, $db_location[1]);
        } else {
            $this->db_id = mysqli_connect($db_location[0], $db_user, $db_pass, $db_name);
        }
        if (!$this->db_id) {
            if ($show_error == 1) {
                $this->display_error(mysqli_connect_error(), '1');
            } else {
                return false;
            }
        }
        $this->mysql_version = mysqli_get_server_info($this->db_id);
        if (!defined('COLLATE')) {
            define("COLLATE", "utf8");
        }
        mysqli_query($this->db_id, "SET NAMES '" . $this->db_config['collate'] . "'");
        return true;
    }

    /**
     * @param $query
     * @param bool $show_error
     * @return \mysqli_result
     */
    function query($query, $show_error = true)
    {

        $config = $this->db_config;

        $time_before = $this->get_real_time();
        if (!$this->db_id) $this->connect($config['dbuser'], $config['dbpass'], $config['dbname'], $config['dbhost']);
        if (!($this->query_id = mysqli_query($this->db_id, $query))) {
            $this->mysql_error = mysqli_error($this->db_id);
            $this->mysql_error_num = mysqli_errno($this->db_id);
            if ($show_error) {
                $this->display_error($this->mysql_error, $this->mysql_error_num, $query);
            }
        }
        $this->MySQL_time_taken+= $this->get_real_time() - $time_before;
        $this->query_num++;
        return $this->query_id;
    }

    /**
     * @param string $query_id
     * @return array|string[]|null
     */
    function get_row($query_id = '') : array|string|null
    {
        if ($query_id == '') $query_id = $this->query_id;
        return is_object($query_id) ? mysqli_fetch_assoc($query_id) : [];
    }

    /**
     * @param string $query_id
     * @return array|null
     */
    function get_array($query_id = '') : array|null
    {
        if ($query_id == '') $query_id = $this->query_id;
        return mysqli_fetch_array($query_id);
    }

    /**
     * @param $query
     * @param bool $multi
     * @param bool $cache_prefix
     * @param bool $system_cache
     * @return array|mixed|string[]
     */
    function super_query($query, $multi = false) : array
    {
        if (!$multi) {
            $this->query($query);
            $data = $this->get_row();
            $this->free();
            return $data ? $data : [];
        } else {
            $this->query($query);
            $rows = array();
            while ($row = $this->get_row()) {
                $rows[] = $row;
            }
            $this->free();
            return $rows ? $rows : [];
        }
    }

    /**
     * @param string $query_id
     * @return int
     */
    function num_rows($query_id = '')
    {
        if ($query_id == '') $query_id = $this->query_id;
        return mysqli_num_rows($query_id);
    }

    /**
     * @return int|string
     */
    function insert_id() : int
    {
        return mysqli_insert_id($this->db_id);
    }

    /**
     * @param string $query_id
     * @return mixed
     */
    function get_result_fields($query_id = '') : array
    {
        if ($query_id == '') $query_id = $this->query_id;
        while ($field = mysqli_fetch_field($query_id)) {
            $fields[] = $field;
        }
        return $fields;
    }

    /**
     * @param $source
     * @return string
     */
    function safesql($source) : string
    {
        $config = $this->db_config;
        if (!$this->db_id)
            $this->connect($config['dbuser'], $config['dbpass'], $config['dbname'], $config['dbhost']);
        return mysqli_real_escape_string($this->db_id, $source);
    }

    /**
     * @param string $query_id
     */
    function free($query_id = '')
    {
        if ($query_id == '')
            $query_id = $this->query_id;
        return mysqli_free_result($query_id);
    }

    /**
     * close bd
     */
    function close() : bool
    {
//         return mysqli_close($this->db_id);
        return true;
    }

    /**
     * @return float
     */
    function get_real_time() : float
    {
        list($seconds, $microSeconds) = explode(' ', microtime());
        return ((float)$seconds + (float)$microSeconds);
    }

    /**
     * @param $error
     * @param $error_num
     * @param string $query
     */
    function display_error($error, $error_num, $query = '')
    {
        $name = $filename = __DIR__.'/../../../../../app/cache/system/out.tmp';
        $file = Log::factory('File', $filename, 'DB');
        $file->log('Ошибка сервера: ' . $query . ' ' . $error_num);

    }
}
