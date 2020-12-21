<?php

namespace Sura\Cache\Adapter;

use Sura\Cache\Contracts\CacheItemInterface;
use Sura\Cache\Contracts\CacheItemPoolInterface;
use Sura\Cache\Exeption\InvalidArgumentException;

class MemcachedAdapter extends AbstractAdapter implements CacheItemPoolInterface
{
    protected $server = null;
    protected $suite_key = null;
    protected $cache_info_key = null;
    protected $server_type = null;
    protected $max_age = null;
    public $connection = null;

    /**
     * load settings
     *
     * @param $config
     */
    public function init($config)
    {
        $this->suite_key = md5( $config['dbname'] . $config['prefix'] . $config['secure_auth_key'] );
        $this->cache_info_key = md5( $this->suite_key. '_all_info_tags_' );

        $this->server = $this->connect();

        if($this->connection !== -1 ) {
            $memcache_server = explode(":", $config['memcache_server']);
            $this->connection = 1;
            if ($memcache_server['0'] == 'unix') {
                $memcache_server = array($config['memcache_server'], 0);
            }

            if ( !$this->server->addServer($memcache_server['0'], $memcache_server['1']) ) {
                $this->connection = 0;
            }

            if ( $this->server->getStats() === false ) {
                $this->connection = 0;
            }

            if($this->connection > 0 AND $this->server_type == "memcached") {
                $this->server->setOption(Memcached::OPT_COMPRESSION, false);
            }
        }

        if ( $config['clear_cache'] )
            $this->max_age = $config['clear_cache'] * 60;
        else
            $this->max_age = 86400;
    }

    /**
     * connect to memcache server
     *
     * @return Memcache|Memcached
     */
    protected function connect() {
        if( class_exists( 'Memcached' ) ) {
            $this->server_type = "memcached";
            return new Memcached();
        } elseif( class_exists( 'Memcache' ) ) {
            $this->server_type = "memcache";
            return new Memcache();
        } else {
            $this->connection = -1;
        }

    }

    /**
     * @param $key
     * @return mixed
     */
    public function getItem($key)
    {
        if($this->connection < 1 )
            return false;

        $key = $this->hasItem($key);
        try {
            return $this->server->get($key);
        }catch (CacheException $e){
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $keys
     * @return mixed
     */
    public function getItems(array $keys = array())
    {
        return true;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function hasItem($key) : bool
    {
        return $key = md5( $this->suite_key.$key );
    }

    /**
     * @return mixed
     */
    public function clear()
    {
        return true;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function deleteItem($key)
    {
        return $this->server->delete($key);
    }

    /**
     * @param array $keys
     * @return mixed
     */
    public function deleteItems(array $keys)
    {
        return true;
    }

    /**
     * @param CacheItemInterface $item
     * @return mixed
     */
    public function save($item)
    {
//        $encodedValues = [];
        foreach ($item as $key => $value) {

            if($this->connection < 1 )
                return false;

            $key_name = $this->hasItem($key);

            $this->_set( $key_name, $value );
            $this->_setstoredkeys( $key_name, $key );

        }

        return true;
    }

    /**
     * @param $key_name
     * @param $key
     * @return bool
     */
    protected function _setstoredkeys($key_name, $key) {

        if($this->connection < 1 )
            return false;

        $cache_keys = json_decode($this->server->get($this->cache_info_key), true);

        if( !is_array($cache_keys) ) $cache_keys = array();

        $cache_keys[$key_name] = $key;

        $this->_replaceset( $this->cache_info_key, json_encode($cache_keys) );

    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    protected function _set($key, $value) {
        if($this->connection < 1 ) return false;
        if ( $this->server_type == "memcache" ) {
            $this->server->set( $key, $value, null, $this->max_age );
        } else {
            $this->server->set( $key, $value, $this->max_age);
        }
        return true;
    }

    /**
     * @param CacheItemInterface $item
     * @return mixed
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function commit()
    {
        return true;
    }

    /**
     * exit
     */
    public function __destruct() {

        if($this->connection < 1 ) return;

        if( $this->server ) {
            if( method_exists( $this->server, 'quit' ) ) {
                $this->server->quit();
            } elseif( method_exists( $this->server, 'close' ) ) {
                $this->server->close();
            }
        }
    }
}