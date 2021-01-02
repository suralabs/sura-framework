<?php

namespace Sura\Url\Exceptions;

use InvalidArgumentException;

/**
 * Class InvalidArgument
 * @package Sura\Url\Exceptions
 */
class InvalidArgument extends InvalidArgumentException
{
    /**
     * @param string $url
     * @return static
     */
    public static function invalidScheme(string $url): self
    {
        return new static("The scheme `{$url}` isn't valid. It should be either `http` or `https`.");
    }

    /**
     * @return static
     */
    public static function segmentZeroDoesNotExist()
    {
        return new static("Segment 0 doesn't exist. Segments can be retrieved by using 1-based index or a negative index.");
    }
}
