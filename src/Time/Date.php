<?php


namespace Sura\Time;


use Sura\Libs\Langs;
use Sura\Libs\Request;
use Sura\Utils\DateTime;

class Date
{
    //TODO l18n
	/**
     *  generate date
     *
	 * @param int $timestamp - date
	 * @param false|string $func - no_year
	 * @param bool $full - full
	 * @return string
	 */
	public static function megaDate(int $timestamp, false|string $func = false, bool $full = false): string
	{
		$server_time = (int)$_SERVER['REQUEST_TIME'];
		if (date('Y-m-d', $timestamp) == date('Y-m-d', $server_time)) {
			return Langs::lang_date('сегодня в H:i', $timestamp);
		}
		if (date('Y-m-d', $timestamp) == date('Y-m-d', ($server_time - 84600))) {
			return Langs::lang_date('вчера в H:i', $timestamp);
		}
		if ($func == 'no_year') {
			return Langs::lang_date('j M в H:i', $timestamp);
		}
		if ($full) {
			return Langs::lang_date('j F Y в H:i', $timestamp);
		}
		return Langs::lang_date('j M Y в H:i', $timestamp);
	}

    /**
     * Get server time
     */
    public static function time(): int
    {
        $server = Request::getRequest()->server;
        return (int)$server['REQUEST_TIME'];
    }

    /**
     * @param $timestamp - date
     * @param string $format - to format
     * @return string
     * @throws \Exception
     */
    public static function date_convert($timestamp, string $format): string
    {
        if (is_numeric($timestamp)) {
            $date = new DateTime();
            $date->setTimestamp($timestamp);
        } else {
            $date = new DateTime($timestamp);
        }
        $date = $date->format('Y-m-d H:i:s');

        $date = new DateTime($date);
        return $date->format($format);
    }
}