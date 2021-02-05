<?php

declare(strict_types=1);

namespace Sura\Contracts;

/**
 * Авторизация пользователей
 */
interface AuthInterface
{
    /**
     * @return array
     */
    public static function index(): array;

    /**
     * logout site
     * @param bool $redirect
     */
    public static function logout($redirect = false): void;
}