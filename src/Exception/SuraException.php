<?php
declare(strict_types=1);


namespace Sura\Exception;


use JetBrains\PhpStorm\Pure;

/**
 *
 */
class SuraException extends \InvalidArgumentException
{
	
	/**
	 * @param string $message
	 * @return static
	 */
	#[Pure] public static function error(string $message): self
	{
		return new static($message);
	}

    /**
     * @return string
     */
    public static function err(): string
    {
		return '';
	}
}