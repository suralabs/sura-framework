<?php

/*
 * Copyright (c) 2023 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\Exception;

use Exception;
use JetBrains\PhpStorm\Pure;

/**
 * A Abstration of Excecption to include the __toString function
 *
 */
class AbstractException extends Exception
{
    /**
     * @var string
     */
    private string $soapFault;

    /**
     * @param string $message Error description $message
     * @param int $code HTTP Error code $code
     */
    #[Pure] public function __construct(string $message = '', int $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * @return string
     */
    public function getSoapFault(): string
    {
        return $this->soapFault;
    }

    /**
     * @param string $soapFault
     * @return string
     */
    public function setSoapFault(string $soapFault): string
    {
        $this->soapFault = $soapFault;
        return $soapFault;
    }

    /**
     * Returns a formatted string of the error code and message
     *
     * @return string
     */
    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
