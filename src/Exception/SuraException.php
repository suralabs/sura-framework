<?php


namespace Sura\Exception;


use JetBrains\PhpStorm\Pure;

class SuraException extends \InvalidArgumentException
{

    /**
     * @param string $message
     * @return static
     */
    #[Pure] public static function Error(string $message): self
    {
        return new static( $message);
    }
}