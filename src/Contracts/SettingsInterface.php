<?php

declare(strict_types=1);

namespace Sura\Contracts;

interface SettingsInterface
{
    public static function load() : array;
}