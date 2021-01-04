<?php

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

