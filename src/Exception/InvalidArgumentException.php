<?php

/*
 * Copyright (c) 2023 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\Exception;

use JetBrains\PhpStorm\Pure;

/**
 *
 */
class InvalidArgumentException extends AbstractException
{
    /**
     * @param string $message
     * @param int $code
     */
    #[Pure] public function __construct(string $message = '', int $code = 500)
    {
        if (!$message) {
            $message = "We encountered an internal error. Please try again.";
        }
        parent::__construct($message, $code);
    }
}
