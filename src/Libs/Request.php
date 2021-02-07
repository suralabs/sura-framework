<?php
declare(strict_types=1);

namespace Sura\Libs;


use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Sura\Contracts\RequestInterface;

/**
 * Class Request
 * @package Sura\Libs
 */
class Request implements RequestInterface
{
    /** @var \Sura\Libs\Request|null */
    private static Request|null $requests = null;

    /**
     * хранит значение полученное методами
     *
     * @param array $get
     * @param array $post
     * @param array $files
     * @param array $cookie
     * @param array $session
     * @param array $request
     * @param array $server
     * @param array $header
     */
    #[NoReturn] public function __construct(
        public array $get = array(),
        public array $post = array(),
        public array $files = array(),
        public array $cookie = array(),
        public array $session = array(),
        public array $request = array(),
        public array $server = array(),
        public array $header = array()
    )
    {
        $this->initWithFastCGI();

    }

    public static function getRequest() : Request
    {
        if (self::$requests == null) {
            self::$requests = new Request();
        }
        return self::$requests;
    }

    /**
     *
     */
    public function setGlobal()
    {
        /**
         * Назначает информацию заголовка HTTP суперглобальной переменной $ _SERVER
         */
        foreach ($this->header as $key => $value)
        {
            $_key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
            $this->server[$_key] = $value;
        }
        $_GET = $this->get;
        $_POST = $this->post;
//        $_FILES = $this->files;
        $_COOKIE = $this->cookie;
        $_SERVER = $this->server;

        //$this->request = $_REQUEST = array_merge($this->get, $this->post, $this->cookie);
        $this->request = $_REQUEST = array_merge($this->get, $this->post, $this->cookie);
        $this->request = $_REQUEST = array_merge($this->cookie, $this->get, $this->post );
    }

    /**
     * @return mixed
     */
    public function getGlobal(): array
    {
        return $this->request;
    }

    /**
     * Инициализация
     */
    #[NoReturn] public function initWithFastCGI(): void
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->cookie = $_COOKIE;
        $this->server = $_SERVER;
        $this->request = $_REQUEST;
    }

    /**
     *
     */
    #[NoReturn] public function unsetGlobal(): void
    {
//        $_REQUEST = $_SESSION = $_COOKIE = $_FILES = $_POST = $_SERVER = $_GET = array();
//        $_REQUEST = $_COOKIE = $_FILES = $_POST = $_SERVER = $_GET = array();
        $_REQUEST = $_COOKIE = $_POST = $_SERVER = $_GET = array();
    }

    /**
     * @return bool
     */
    #[Pure] public function isWebSocket(): bool
    {
        return isset($this->header['Upgrade']) && strtolower($this->header['Upgrade']) == 'websocket';
    }

    /**
     * Получить IP клиента
     *
     * @return string - User IP
     */
    #[Pure] public function getClientIP() : string
    {
        if (isset($this->server["HTTP_X_REAL_IP"]) and strcasecmp($this->server["HTTP_X_REAL_IP"], "unknown"))
        {
            return $this->server["HTTP_X_REAL_IP"];
        }
        if (isset($this->server["HTTP_CLIENT_IP"]) and strcasecmp($this->server["HTTP_CLIENT_IP"], "unknown"))
        {
            return $this->server["HTTP_CLIENT_IP"];
        }
        if (isset($this->server["HTTP_X_FORWARDED_FOR"]) and strcasecmp($this->server["HTTP_X_FORWARDED_FOR"], "unknown"))
        {
            return $this->server["HTTP_X_FORWARDED_FOR"];
        }
        if (isset($this->server["REMOTE_ADDR"]))
        {
            return $this->server["REMOTE_ADDR"];
        }
        return "";
    }

    /**
     * Получить USER_AGENT клиента
     *
     * @return string
     */
    public function getClientAGENT() : string
    {
        if (isset($this->server["HTTP_USER_AGENT"]))
        {
            return $this->server["HTTP_USER_AGENT"];
        }
        return "";
    }

    #[Pure] public function getMethod(): string
    {
        return getenv('REQUEST_METHOD');
    }

    /**
     * Проверяем ajax
     *
     * @return bool
     */
    public function checkAjax() : bool
    {
        return isset($this->post['ajax']) and $this->post['ajax'] == 'yes';
    }

    static public function newcheckAjax()
    {
        $json = file_get_contents('php://input');
        if (!empty($json)){
            $obj = json_decode($json, TRUE);

            if ($obj['ajax'] == 'yes'){
                return true;
            }
        }

//        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
//            // Если к нам идёт Ajax запрос, то ловим его
//            return true;
//        }else
            if (isset($_POST['ajax']) and $_POST['ajax'] == 'yes'){
            return true;
        }
        return false;

    }

    /**
     * Проверяем ajax
     *
     * @return bool
     */
    public static function ajax() : bool
    {
        return isset($_POST['ajax']) and $_POST['ajax'] == 'yes';
    }

    /**
     * Проверяем https
     *
     * @return bool
     */
    public static function https() : bool
    {
        return !(getenv['SERVER_PORT'] !== 443);
    }
}