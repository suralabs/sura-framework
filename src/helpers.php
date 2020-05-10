<?php

use Sura\Libs\Str;

if (!function_exists('e')) {
    /**
     * Escape HTML entities in a string.
     *
     * @param  string  $value
     * @return string
     */
    function e($value)
    {
        return html_entity_decode($value);
    }
}

if (!function_exists('clean_url')) {
    /**
     * FUNC. COOKIES
     *
     * @param $url
     * @return bool
     */
    function clean_url($url)
    {
        if ($url == '') return false;
        $url = str_replace( "http://", "", strtolower( $url ) );
        $url = str_replace("https://", "", $url);
        if( substr( $url, 0, 4 ) == 'www.' ) $url = substr( $url, 4 );
        $url = explode('/', $url);
        $url = reset($url);
        $url = explode(':', $url);
        $url = reset($url);
        return $url;
    }
}

if (!function_exists('GetVar')) {

    /**
     * @param string $v
     * @return string
     */
    function GetVar(string $v): string
    {
        if (ini_get('magic_quotes_gpc'))
            return stripslashes($v);
        return $v;
    }
}
