<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\Http;

use JsonException;

class Response
{
    /**
     * @throws JsonException
     */
    public function _e_json(mixed $value): void
    {
        header('Content-Type: application/json');
        echo json_encode($value, JSON_THROW_ON_ERROR);
    }
}