<?php


namespace Sura\Contracts;


use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Sura\Libs\Request;

interface RequestInterface
{
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
    public function __construct(array $get = array(), array $post = array(), array $files = array(), array $cookie = array(), array $session = array(), array $request = array(), array $server = array(), array $header = array());

    /**
     * Проверяем https
     *
     * @return bool
     */
    public static function https(): bool;

    /**
     * Проверяем ajax
     *
     * @return bool
     */
    public static function ajax(): bool;

    public static function newcheckAjax();

    public static function getRequest(): Request;

    /**
     * @return mixed
     */
    public function getGlobal(): array;

    /**
     * Получить USER_AGENT клиента
     *
     * @return string
     */
    public function getClientAGENT(): string;

    /**
     * Получить IP клиента
     *
     * @return string - User IP
     */
    public function getClientIP(): string;

    /**
     * Инициализация
     */
    public function initWithFastCGI(): void;

    /**
     * @return bool
     */
    public function isWebSocket(): bool;

    /**
     *
     */
    public function setGlobal();

    /**
     *
     */
    public function unsetGlobal(): void;

    public function getMethod(): string;

    /**
     * Проверяем ajax
     *
     * @return bool
     */
    public function checkAjax(): bool;
}