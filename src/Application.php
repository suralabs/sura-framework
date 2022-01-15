<?php
declare(strict_types=1);

namespace Sura;

use Exception;
use Sura\Libs\Settings;
use Throwable;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Sura\Container\Container;
use Sura\Libs\Router;

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
     * Application constructor.
     * @param string|null $base_path
     */
    public function __construct(string|null $base_path = null)
    {
        if ($base_path) {
            $this->setBasePath($base_path);
        }

        $this->registerBaseBindings();
        $this->registerCoreContainerAliases();

    }

    /**
     *
     */
    public function handle(): void
    {
        Libs\Users::userOnline();

        try {
            $this->routing();
        } catch (Exception $error) {
            $class = 'App\Modules\ErrorController';
            $controller = new $class();
            $params['error'] = 'Error: ' . $error->getLine();
            $params['error_name'] = 'Error: ' . $error->getMessage();
            call_user_func_array([$controller, 'index'], [$params]);
        } catch (Throwable $error) {
            $class = 'App\Modules\ErrorController';
            $controller = new $class();
            $params['error'] = 'Error: ' . $error->getLine() . $error->getFile();
            $params['error_name'] = $error->getMessage();
            call_user_func_array([$controller, 'index'], [$params]);
        }
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

    /**
     *
     */
    protected function registerBaseBindings(): void
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->instance(Container::class, $this);
    }

    /**
     *
     */
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
        $params = [];
        $routers = require $this->get('path.base') . '/routes/web.php';
        $router->add($routers);
        try {
            if ($router->isFound()) {
                $router->executeHandler($router::getRequestHandler(), $params);
            } else {
                http_response_code(404);
                $class = 'App\Modules\ErrorController';
                $controller = new $class();
//                throw SuraException::Error("Page not found");
//                echo call_user_func_array([$controller, 'index'], [$params]);
            }
        } catch (InvalidArgumentException $error) {
            echo 'Error: ' . $error->getMessage();
        }

    }

    /**
     * @param string $basePath
     * @return $this
     */
    public function setBasePath(string $basePath): static
    {
        $this->basePath = rtrim($basePath, '\/');

        $this->bindPaths();

        return $this;
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @return void
     */
    protected function bindPaths(): void
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
    public function path(string $path = ''): string
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
    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the bootstrap directory.
     *
     * @param string $path Optionally, a path to append to the bootstrap path
     * @return string
     */
    public function bootstrapPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'bootstrap' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the application configuration files.
     *
     * @param string $path Optionally, a path to append to the config path
     * @return string
     */
    public function configPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the database directory.
     *
     * @param string $path Optionally, a path to append to the database path
     * @return string
     */
    public function databasePath(string $path = ''): string
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
    public function resourcePath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}