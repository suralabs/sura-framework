<?php


namespace Sura\Time;


use Sura\Libs\Langs;
use Sura\Libs\Request;
use Sura\Utils\DateTime;

/**
 *
 */
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
        $online = 84600;
		$server_time = (int)$_SERVER['REQUEST_TIME'];
		if (date('Y-m-d', $timestamp) == date('Y-m-d', $server_time)) {
			return Langs::langDate('сегодня в H:i', $timestamp);
		}
		if (date('Y-m-d', $timestamp) == date('Y-m-d', ($server_time - $online))) {
			return Langs::langDate('вчера в H:i', $timestamp);
		}
		if ($func == 'no_year') {
			return Langs::langDate('j M в H:i', $timestamp);
		}
		if ($full) {
			return Langs::langDate('j F Y в H:i', $timestamp);
		}
		return Langs::langDate('j M Y в H:i', $timestamp);
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
    public static function dateConvert($timestamp, string $format): string
    {
        if (is_numeric($timestamp)) {
            $date_time = new DateTime();
            $date_time->setTimestamp($timestamp);
        } else {
            $date_time = new DateTime($timestamp);
        }
        $date_time = $date_time->format('Y-m-d H:i:s');

        $date_time = new DateTime($date_time);
        return $date_time->format($format);
    }
}