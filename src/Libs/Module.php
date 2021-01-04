<?php  
namespace Sura\Libs;

use Sura\Contracts\ModuleInterface;

/**
 *  Module
 *
 */
class Module implements ModuleInterface
{
    /**
     * @return string|array|null
     */
    public function user_info() : string|array|null
    {
	    return Registry::get('user_info');
    }

    /**
     * @return array|null
     */
    function logged() : string|null
    {
        return Registry::get('logged');
    }

    /**
     * @return \Sura\Libs\Db|null
     */
    function db() : null|Db
    {
        return Db::getDB();
    }

    /**
     * @return array
     */
    function get_langs() : array
    {
        return langs::get_langs();
    }
}
