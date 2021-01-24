<?php
declare(strict_types = 1);

namespace Sura;

use InvalidArgumentException;
use Sura\Exception\SuraException;
use Sura\Libs\Db;
use Sura\Libs\Router;

/**
 * 
 */
class Console
{
	
	function __construct($basePath = null)
	{
        $params = array();
        $this->routing($params);
	}

    /**
     * VERSION
     */
    public const VERSION = '1.0.0';

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version(): string
    {
        return static::VERSION;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function routing($params): mixed
    {
		$go = $_SERVER['argv']['1'];
        $res = $path = explode('-', $go);
        $res = $res['1'];
        $url =  explode(':', $res);
        $controllerName = ucfirst($url['0'].'Controller');
        $actionName = 'Console'.ucfirst($url['1']);
        $class = 'App\Modules\\'.$controllerName;
        $foo = new $class();
		return call_user_func_array(array($foo, $actionName), $params);
    }
}
