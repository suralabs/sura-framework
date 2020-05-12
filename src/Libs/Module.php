<?php  
namespace Sura\Libs;

use Sura\Classes\Db;
use Sura\Contracts\ModuleInterface;
use Sura\Libs\Langs;
use Sura\Libs\Registry;

/**
 *  Module
 *
 */
class Module implements ModuleInterface
{
    /**
     * @return unknown|null
     */
    public function user_info(){
	    return Registry::get('user_info');
    }

    /**
     * @return unknown|null
     */
    function logged(){
        return Registry::get('logged');
    }

    /**
     * @return mixed
     */
    function db(){
        return Db::getDB();
    }

    /**
     * @return array
     */
    function get_langs(){
        return langs::get_langs();
    }
}
