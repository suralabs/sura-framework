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
	public function user_info(){
	    return Registry::get('user_info');
    }

    function logged(){
        return Registry::get('logged');
    }

    function db(){
        return Db::getDB();
    }

    function get_langs(){
        return langs::get_langs();
    }
}
