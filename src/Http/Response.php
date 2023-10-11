<?php
/*
 * Copyright (c) 2023 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\Http;

use JsonException;

/**
 *
 */
class Response
{
    /**
     * Json response
     * @throws JsonException
     */
    public function _e_json(mixed $value): void
    {
        header('Content-Type: application/json');
        echo json_encode($value, JSON_FORCE_OBJECT);
    }
}