<?php


namespace Sura\Contracts;


use Sura\Exception\SuraException;
use Sura\Libs\Request;
use Sura\Libs\Router;
use Sura\Libs\Settings;

interface RouterInterface
{
    /**
     * Router constructor.
     * @param string $uri
     * @param string $method
     */
    public function __construct(string $uri, string $method = 'GET');

    /**
     * Request method.
     * @return string
     */
    public static function getRequestMethod(): string;

    /**
     * Request params.
     * @return string
     */
    public static function getActionName(): string;

    /**
     * Request params.
     * @return string
     */
    public static function getControllerName(): string;

    /**
     * Request wildcard params.
     * @return array
     * @deprecated
     */
    public static function getParams(): array;

    /**
     * Get Request handler.
     * @return string
     */
    public static function getRequestHandler(): string;

    /**
     * Current .
     * @return array
     */
    public static function getRoutes(): array;

    /**
     * Factory method construct Router from global vars.
     * @return Router
     */
    public static function fromGlobals(): Router;

    /**
     * Current processed URI.
     * @return string
     */
    public static function getRequestUri(): string;

    /**
     * Execute Request Handler.
     * Запуск соответствующего действия/экшена/метода контроллера
     *
     * @param callable|null|string $handler
     * @param array $params
     * @return mixed
     */
    public function executeHandler(callable|null|string $handler = null, array $params = array()): mixed;

    /**
     * Add route rule.
     *
     * @param string|array $route A URI route string or array
     * @param callable|string|null $handler Any callable or string with controller classname and action method like "ControllerClass@actionMethod"
     * @return Router
     */
    public function add(array|string $route, callable|null|string $handler = null): Router;

    /**
     * Process requested URI.
     * @return bool
     */
    public function isFound(): bool;

    /**
     * Set Request handler.
     * @param $handler string|callable
     */
    public function setRequestHandler(string|callable $handler);
}