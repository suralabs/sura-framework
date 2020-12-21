<?php


namespace Sura\Libs;


class Request
{
    /**
     * Request constructor.
     * @param array $get
     * @param array $post
     * @param array $files
     * @param array $cookie
     * @param array $session
     * @param $request
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

    }

    /**
     *
     */
    function setGlobal()
    {
        /**
         * 将HTTP头信息赋值给$_SERVER超全局变量
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
    function getGlobal()
    {
        return $this->request;
    }

    /**
     * LAMP环境初始化
     */
    function initWithFastCGI()
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
    function unsetGlobal()
    {
        $_REQUEST = $_SESSION = $_COOKIE = $_FILES = $_POST = $_SERVER = $_GET = array();
    }

    /**
     * @return bool
     */
    function isWebSocket()
    {
        return isset($this->header['Upgrade']) && strtolower($this->header['Upgrade']) == 'websocket';
    }

    /**
     * @return mixed|string
     */
    function getClientIP()
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
     * 
     *
     * @return bool
     */
    public static function ajax(){
        if (isset($_POST['ajax']) AND $_POST['ajax'] == 'yes')
            return true;
        else
            return false;
    }

    public static function https()
    {
        if($_SERVER['SERVER_PORT'] != 443) {
            return false;
        }else{
            return true;
        }
    }




}