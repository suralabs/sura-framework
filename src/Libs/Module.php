<?php  
namespace Sura\Libs;

use Sura\Libs\Db;
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
     * @return array|null
     */
    public function user_info(){
	    return Registry::get('user_info');
    }

    /**
     * @return array|null
     */
    function logged(){
        return Registry::get('logged');
    }

    /**
     * @return object
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
