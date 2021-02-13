<?php
declare(strict_types=1);

namespace Sura;

use Exception;
use Sura\Libs\Settings;
use Throwable;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Sura\Container\Container;
use Sura\Libs\Db;
use Sura\Libs\Registry;
use Sura\Libs\Router;
use Sura\Libs\Tools;

/**
 * Class Application
 * @package Sura
 */
class Application extends Container
{
	/**
	 * VERSION
	 */
	public const VERSION = '1.0.0';
	
	/**
	 * The base path for the Sura installation.
	 *
	 * @var string
	 */
	protected string $basePath;
	
	/**
	 * The custom application path defined by the developer.
	 *
	 * @var string
	 */
	protected string $appPath;
	
	/**
	 * The custom database path defined by the developer.
	 *
	 * @var string
	 */
	protected string $databasePath;
	
	/**
	 * The custom storage path defined by the developer.
	 *
	 * @var string
	 */
	protected string $storagePath;
	
	
	/**
	 * The bootstrap classes for the application.
	 *
	 * @var string[]
	 */
	protected $bootstrappers = [\Sura\Bootstrap\Provider::class,];
	
	/**
	 * Indicates if the application has "booted".
	 *
	 * @var bool
	 */
	protected $booted = false;
	
	/**
	 * The array of booting callbacks.
	 *
	 * @var callable[]
	 */
	protected $bootingCallbacks = [];
	
	/**
	 * The array of booted callbacks.
	 *
	 * @var callable[]
	 */
	protected $bootedCallbacks = [];
	
	/**
	 * All of the registered service providers.
	 *
	 * @var \Support\ServiceProvider[]
	 */
	protected $serviceProviders = [];
	
	/**
	 * Application constructor.
	 * @param string|null $basePath
	 */
	public function __construct(string|null $basePath = null)
	{
		if ($basePath) {
			$this->setBasePath($basePath);
		}
		
		$this->registerBaseBindings();
		$this->registerCoreContainerAliases();
		
	}
	
	public function handle(): void
	{
		$this->user_online();
		
		try {
			$this->routing();
		} catch (Exception $e) {
			var_dump($e);
		} catch (Throwable $e) {
			var_dump($e);
		}
	}
	
	/**
	 * Get the bootstrap classes for the application.
	 *
	 * @return array
	 */
	protected function bootstrappers()
	{
		return $this->bootstrappers;
	}
	
	/**
	 * Get the version number of the application.
	 *
	 * @return string
	 */
	public function version(): string
	{
		return static::VERSION;
	}
	
	protected function registerBaseBindings(): void
	{
		static::setInstance($this);
		
		$this->instance('app', $this);
		
		$this->instance(Container::class, $this);
	}
	
	public function registerCoreContainerAliases(): void
	{
		foreach (['app' => [self::class], 'config' => [Settings::class],
		
		         ] as $key => $aliases) {
			foreach ($aliases as $alias) {
				$this->alias($key, $alias);
			}
		}
	}
	
	/**
	 * @throws \Exception
	 */
	public function routing()
	{
		$router = Router::fromGlobals();

//        $this->get('path.base');
		
		$params = array();
		$routers = require $this->get('path.base') . '/routes/web.php';
		$router->add($routers);
		try {
			if ($router->isFound()) {
				$router->executeHandler($router->getRequestHandler(), $params);
			} else {
				http_response_code(404);
				$class = 'App\Modules\ErrorController';
				$foo = new $class();
				echo call_user_func_array(array($foo, $action = 'Index'), array());
//                throw SuraException::Error("Page not found");
			}
		} catch (InvalidArgumentException $e) {
			echo $e->getMessage();
		}
		
	}
	
	/**
	 * @return bool
	 */
	public function user_online(): bool
	{
		$logged = Registry::get('logged');
		
		//Если юзер залогинен то обновляем последнюю дату посещения на личной стр
		if ($logged) {
			$user_info = Registry::get('user_info');
			
			//Начисления 1 убм.
			if (!$user_info['user_lastupdate']) {
				$user_info['user_lastupdate'] = 1;
			}

//            $server_time = intval($_SERVER['REQUEST_TIME']);
            $server_time = \Sura\Libs\Date::time();
			
			if (date('Y-m-d', (int)$user_info['user_lastupdate']) < date('Y-m-d', $server_time)) {
				$sql_balance = ", user_balance = user_balance+1, user_lastupdate = '{$server_time}'";
			} else {
				$sql_balance = "";
			}
			
			//Определяем устройство
			$device_user = 0;
			if (($user_info['user_last_visit'] + 60) <= $server_time) {
				$db = Db::getDB();
				$db->query("UPDATE LOW_PRIORITY `users` SET user_logged_mobile = '{$device_user}', user_last_visit = '{$server_time}' {$sql_balance} WHERE user_id = '{$user_info['user_id']}'");
			}
			return true;
		}
		return true;
	}
	
	public function setBasePath(string $basePath): static
	{
		$this->basePath = rtrim($basePath, '\/');
		
		$this->bindPathsInContainer();
		
		return $this;
	}
	
	/**
	 * Bind all of the application paths in the container.
	 *
	 * @return void
	 */
	protected function bindPathsInContainer(): void
	{
		$this->instance('path', $this->path());
		$this->instance('path.base', $this->basePath());
		$this->instance('path.lang', $this->langPath());
		$this->instance('path.config', $this->configPath());
		$this->instance('path.public', $this->publicPath());
		$this->instance('path.storage', $this->storagePath());
		$this->instance('path.database', $this->databasePath());
		$this->instance('path.resources', $this->resourcePath());
		$this->instance('path.bootstrap', $this->bootstrapPath());
	}
	
	/**
	 * Get the path to the application "app" directory.
	 *
	 * @param string $path
	 * @return string
	 */
	public function path($path = ''): string
	{
		$appPath = $this->appPath = (string)$this->basePath . DIRECTORY_SEPARATOR . 'app';
		return $appPath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
	}
	
	/**
	 * Get the base path of the Laravel installation.
	 *
	 * @param string $path Optionally, a path to append to the base path
	 * @return string
	 */
	public function basePath($path = ''): string
	{
		return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
	}
	
	/**
	 * Get the path to the bootstrap directory.
	 *
	 * @param string $path Optionally, a path to append to the bootstrap path
	 * @return string
	 */
	public function bootstrapPath($path = ''): string
	{
		return $this->basePath . DIRECTORY_SEPARATOR . 'bootstrap' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
	}
	
	/**
	 * Get the path to the application configuration files.
	 *
	 * @param string $path Optionally, a path to append to the config path
	 * @return string
	 */
	public function configPath($path = ''): string
	{
		return $this->basePath . DIRECTORY_SEPARATOR . 'config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
	}
	
	/**
	 * Get the path to the database directory.
	 *
	 * @param string $path Optionally, a path to append to the database path
	 * @return string
	 */
	public function databasePath($path = ''): string
	{
		return ($this->databasePath = $this->basePath . DIRECTORY_SEPARATOR . 'database') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
	}
	
	/**
	 * Set the database directory.
	 *
	 * @param string $path
	 * @return $this
	 */
	public function useDatabasePath(string $path): static
	{
		$this->databasePath = $path;
		
		$this->instance('path.database', $path);
		
		return $this;
	}
	
	/**
	 * Get the path to the language files.
	 *
	 * @return string
	 */
	#[Pure] public function langPath(): string
	{
		return $this->resourcePath() . DIRECTORY_SEPARATOR . 'lang';
	}
	
	/**
	 * Get the path to the public / web directory.
	 *
	 * @return string
	 */
	public function publicPath(): string
	{
		return $this->basePath . DIRECTORY_SEPARATOR . 'public';
	}
	
	/**
	 * Get the path to the storage directory.
	 *
	 * @return string
	 */
	public function storagePath(): string
	{
//        if ($this->storagePath == 1){
		$this->storagePath = $this->basePath . DIRECTORY_SEPARATOR . 'storage';
//        }
		return $this->storagePath;
	}
	
	/**
	 * Set the storage directory.
	 *
	 * @param string $path
	 * @return $this
	 */
	public function useStoragePath(string $path): static
	{
		$this->storagePath = $path;
		
		$this->instance('path.storage', $path);
		
		return $this;
	}
	
	/**
	 * Get the path to the resources directory.
	 *
	 * @param string $path
	 * @return string
	 */
	public function resourcePath($path = ''): string
	{
		return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
	}
}