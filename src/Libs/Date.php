<?php


namespace Sura\Libs;


class Date
{
	/**
     *  generate date
     * TODO l18n
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
}