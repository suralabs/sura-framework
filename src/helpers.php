<?php

/*
 * Copyright (c) 2022 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use JetBrains\PhpStorm\Deprecated;

/**
 * todo update
 * @param string $format
 * @param int $stamp
 * @return string
 */
function langDate(string $format, int $stamp): string
{
    $lang_date = [
        'January' => "января",
        'February' => "февраля",
        'March' => "марта",
        'April' => "апреля",
        'May' => "мая",
        'June' => "июня",
        'July' => "июля",
        'August' => "августа",
        'September' => "сентября",
        'October' => "октября",
        'November' => "ноября",
        'December' => "декабря",
        'Jan' => "янв",
        'Feb' => "фев",
        'Mar' => "мар",
        'Apr' => "апр",
        'Jun' => "июн",
        'Jul' => "июл",
        'Aug' => "авг",
        'Sep' => "сен",
        'Oct' => "окт",
        'Nov' => "ноя",
        'Dec' => "дек",

        'Sunday' => "Воскресенье",
        'Monday' => "Понедельник",
        'Tuesday' => "Вторник",
        'Wednesday' => "Среда",
        'Thursday' => "Четверг",
        'Friday' => "Пятница",
        'Saturday' => "Суббота",

        'Sun' => "Вс",
        'Mon' => "Пн",
        'Tue' => "Вт",
        'Wed' => "Ср",
        'Thu' => "Чт",
        'Fri' => "Пт",
        'Sat' => "Сб",
    ];
    return strtr(date($format, $stamp), $lang_date);
}

/**
 * @param string $text
 * @return string
 */
function strip_data(string $text): string
{
    $quotes = [
        "\x27", "\x22", "\x60", "\t", "\n", "\r", "'", ",", "/", ";", ":", "@", "[", "]", "{", "}", "=", ")",
        "(", "*", "&", "^", "%", "$", "<", ">", "?", "!", '"'];
    $good_quotes = ["-", "+", "#"];
    $rep_quotes = ["\-", "\+", "\#"];
    $text = stripslashes($text);
    $text = trim(strip_tags($text));
    /**
     * @var array<integer,string> $good_quotes
     * @var array<integer,string> $quotes
     * @var array<integer,string> $rep_quotes
     */
    return str_replace([...$quotes, ...$good_quotes], ['', ...$rep_quotes], $text);
}

/**
 * @param string $id
 * @param array $list
 * @return string
 * @since 4.0
 */
function addToList(string $id, array $list): string
{
    $options = '';
    foreach ($list as $key => $value){
        $options.= '<option value="'.$key.'">'.$value.'</option>';
    }
    return str_replace('value="' . $id . '"', 'value="' . $id . '" selected', $options);
}

/**
 * @param int $date
 * @param bool $func
 * @param bool $full
 * @return string
 */
function megaDate(int $date, bool $func = false, bool $full = false): string
{
    if (date('Y-m-d', $date) === date('Y-m-d', time())) {
        return langDate('сегодня в H:i', $date);
    } elseif (date('Y-m-d', $date) === date('Y-m-d', (time() - 84600))) {
        return langDate('вчера в H:i', $date);
    } elseif ($func) {
        //no_year
        return langDate('j M в H:i', $date);
    } elseif ($full) {
        return langDate('j F Y в H:i', $date);
    } else {
        return langDate('j M Y в H:i', $date);
    }
}

/**
 * @param int $number
 * @param array<int> $titles
 * @return string
 */
function declOfNum(int $number, array $titles): string
{
    $cases = [2, 0, 1, 1, 1, 2];
    return (string)$titles[($number % 100 > 4 and $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
}

