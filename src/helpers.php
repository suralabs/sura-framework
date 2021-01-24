<?php

namespace Sura;

use JetBrains\PhpStorm\Pure;

if (!function_exists('e')) {
    /**
     * Escape HTML entities in a string.
     *
     * @param string $value
     * @return string
     */
    #[Pure] function e(string $value): string
    {
        return html_entity_decode($value);
    }
}

if (!function_exists('check_smartphone')) {
    #[Pure] function check_smartphone(): bool
    {

        if (isset($_SESSION['mobile_enable'])) {
            return true;
        }
        $phone_array = array('iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'mobile windows', 'cellphone', 'opera mobi', 'operamobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'symbos', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser', 'android');
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        foreach ($phone_array as $value) {
            if (str_contains($agent, $value)) {
                return true;
            }
        }
        return false;
    }
}

/**
 * Get the available container instance.
 *
 * @param  string|null  $abstract
 * @param  array  $parameters
 * @return mixed
 */
function app($abstract = null, array $parameters = []): mixed
{
    if (is_null($abstract)) {
        return \App\Application::getInstance();
    }

    return \App\Application::getInstance()->make($abstract, $parameters);
}

/**
 * Resolve a service from the container.
 *
 * @param string $name
 * @return mixed
 */
function resolve(string $name): mixed
{
    return \Sura\app($name);
}
