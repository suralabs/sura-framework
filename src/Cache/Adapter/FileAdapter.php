<?php


namespace Sura\Cache\Adapter;


use Sura\Cache\Contracts\CacheItemInterface;
use Sura\Cache\Contracts\CacheItemPoolInterface;

class FileAdapter extends AbstractAdapter implements CacheItemPoolInterface
{
    private string $dir = __DIR__.'/../../../../../app/cache/';

    public function __construct()
    {

    }

    /**
     * @param $value
     */
    protected function _dir($value)
    {
        $this->dir = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getItem($key)
    {
        $filename = $this->_dir.$key.'.tmp';
        $value = file_get_contents($filename);

        $result = [];
        $result[$key] = $value;
        return $result;
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
    public function hasItem($key)
    {
        $filename = $this->_dir.$key.'.tmp';
        if (file_exists($filename))
            return true;
        else
            return false;
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
        $filename = $this->_dir.$key.'tmp';
        if (file_exists($filename)){
            unlink($filename);
            return true;
        }else
            return true;
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
    public function save(CacheItemInterface $item)
    {
        $encodedValues = [];
        foreach ($item as $key => $value) {
            $encodedValues[$key] = $value;
        }
        $filename = $this->_dir.$key.'.tmp';
        $fp = fopen($filename, 'wb+');
        fwrite($fp, $value);
        fclose($fp);
        //chmod($filename, 0666);
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
}