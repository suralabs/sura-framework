<?php
declare(strict_types=1);

namespace Sura;

/**
 *
 */
class Console
{

    /**
     * @param null $basePath
     */
    function __construct($basePath = null)
	{
		$params = [];
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
		$url = explode(':', $res);
		$controller_name = ucfirst($url['0'] . 'Controller');
		$action_name = 'Console' . ucfirst($url['1']);
		$class = 'App\Modules\\' . $controller_name;
		$controller = new $class();
		return call_user_func_array([$controller, $action_name], $params);
	}
}
