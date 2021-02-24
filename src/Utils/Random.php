<?php

declare(strict_types=1);

namespace Sura\Utils;

use Sura;


/**
 * Secure random string generator.
 */
final class Random
{
	use Sura\StaticClass;

    /**
     * Generates a random string of given length from characters specified in second argument.
     * Supports intervals, such as `0-9` or `A-Z`.
     * @param int $length
     * @param string $char_list
     * @return string
     * @throws \Exception
     */
	public static function generate(int $length = 10, string $char_list = '0-9a-z'): string
	{
		$char_list = count_chars(preg_replace_callback('#.-.#', function (array $m): string {
			return implode('', range($m[0][0], $m[0][2]));
		}, $char_list), 3);
		$chLen = strlen($char_list);

		if ($length < 1) {
			throw new Sura\Exception\InvalidArgumentException('Length must be greater than zero.');
		} elseif ($chLen < 2) {
			throw new Sura\Exception\InvalidArgumentException('Character list must contain at least two chars.');
		}

		$res = '';
		for ($i = 0; $i < $length; $i++) {
			$res .= $char_list[random_int(0, $chLen - 1)];
		}
		return $res;
	}
}
