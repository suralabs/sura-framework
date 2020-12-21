<?php

namespace Sura\Cache\Adapter;

use Sura\Cache\Contracts\CacheItemInterface;
use Sura\Cache\Contracts\CacheItemPoolInterface;
use Sura\Cache\Exeption\InvalidArgumentException;

class FileAdapter extends AbstractAdapter implements CacheItemPoolInterface
{
    private string $dir = __DIR__.'/../../../../../../app/cache/';

    /**
     * @param $value
     */
    public function dir($value)
    {
        $this->dir = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getItem($key)
    {
        try {
            $filename = $this->dir.$key.'.tmp';
            if (file_exists($filename))
                return file_get_contents($filename);
            else{
                throw new InvalidArgumentException('item not found');
                return false;
            }
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
    public function hasItem($key)
    {
        $filename = $this->dir.$key.'.tmp';
        if (file_exists($filename))
            return true;
        else
            throw new Exception('item not found');
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
        $filename = $this->dir.$key.'.tmp';
        unlink($filename);
        if (file_exists($filename)){
            throw new InvalidArgumentException('item found');
            return false;
        }else
        {
            return true;
        }
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
    public function save( $item)
    {
        //$encodedValues = [];
        foreach ($item as $key => $value) {
            $filename = $this->dir.$key.'.tmp';
            file_put_contents($filename, $value);
            //chmod($filename, 0666);
            if (!file_exists($filename)){
                throw new InvalidArgumentException('item not found');
            }
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
}