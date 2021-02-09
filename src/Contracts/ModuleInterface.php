<?php

declare(strict_types=1);

namespace Sura\Contracts;

use Sura\Libs\Db;

interface ModuleInterface
{
    public function user_info() : string|array|null;

    public function logged() : bool|null;

    public function db() : null|Db;

    public function get_langs() : array;

}