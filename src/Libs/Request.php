<?php


namespace Sura\Libs;


/**
 * Class Request
 * @package Sura\Libs
 */
class Request
{
    /** @var null  */
    private static $requests = null;

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
    public function __construct(
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
        $_FILES = $this->files;
        $_COOKIE = $this->cookie;
        $_SERVER = $this->server;

        //$this->request = $_REQUEST = array_merge($this->get, $this->post, $this->cookie);
        $this->request = $_REQUEST = array_merge($this->get, $this->post, $this->cookie);
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
    public function initWithFastCGI()
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
    public function unsetGlobal()
    {
//        $_REQUEST = $_SESSION = $_COOKIE = $_FILES = $_POST = $_SERVER = $_GET = array();
        $_REQUEST = $_COOKIE = $_FILES = $_POST = $_SERVER = $_GET = array();
    }

    /**
     * @return bool
     */
    public function isWebSocket(): bool
    {
        return isset($this->header['Upgrade']) && strtolower($this->header['Upgrade']) == 'websocket';
    }

    /**
     * Получить IP клиента
     *
     * @return string - User IP
     */
    public function getClientIP() : string
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

    public function getMethod(): string
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
        if (isset($this->post['ajax']) AND $this->post['ajax'] == 'yes')
            return true;
        else
            return false;
    }

    /**
     * Проверяем ajax
     *
     * @return bool
     */
    public static function ajax() : bool
    {
        if (isset($_POST['ajax']) AND $_POST['ajax'] == 'yes')
            return true;
        else
            return false;
    }

    /**
     * Проверяем https
     *
     * @return bool
     */
    public static function https() : bool
    {
        if(getenv['SERVER_PORT'] != 443) {
            return false;
        }else{
            return true;
        }
    }




}