<?php
declare(strict_types=1);

namespace Sura\Libs;

use Sura\Time\Zone;

/**
 * Class Profile_check
 * @package Sura\Libs
 * @deprecated
 */
class Profile_check
{
	/**
	 * @param $id
	 * @return bool
     * @deprecated
	 */
	public static function time_zone(int $id): bool
	{
		return Zone::zone($id);
	}

    /**
     * @return string
     * @deprecated
     */
	public static function list(): string
	{
		return Zone::list();
	}
}