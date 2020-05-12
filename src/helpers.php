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
     * Дубликат (в tools уже есть)
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

if (!function_exists('msgbox')) {
    /**
     * @param $title
     * @param $text
     * @param $tpl_name
     * @return string|null
     */
    function msgbox($title, $text, $tpl_name)
    {
        global $tpl;
        $result = null;
        if ($tpl_name == 'info') {
            $result = '<div class="err_yellow">' . $text . '</div>';
        } elseif ($tpl_name == 'info_red') {
            $result = '<div class="err_red">' . $text . '</div>';
        } elseif ($tpl_name == 'info_2') {
            $result = '<div class="info_center">' . $text . '</div>';
        } elseif ($tpl_name == 'info_box') {
            $result = '<div class="msg_none">' . $text . '</div>';
        } elseif ($tpl_name == 'info_search') {
            $result = '<div class="margin_top_10"></div><div class="search_result_title" style="border-bottom:1px solid #e4e7eb">Ничего не найдено</div>
    <div class="info_center" style="width:630px;padding-top:140px;padding-bottom:154px">Ваш запрос не дал результатов</div>';
        } elseif ($tpl_name == 'info_yellow') {
            $result = '<div class="err_yellow"><ul class="listing">' . $text . '</ul></div>';
        }
        $tpl->result['info'] .= $result;
        return $result;
    }
}
