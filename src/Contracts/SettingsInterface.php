<?php

declare(strict_types=1);

namespace Sura\Contracts;

interface SettingsInterface
{
    /**
     * load settings
     * @return array
     */
    public static function load(): array;

    /**
     * @param string $parameter
     * @return string
     */
    public static function get(string $parameter): string;
}