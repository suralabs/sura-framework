<?php

declare(strict_types=1);

namespace Sura\Sitemap;

interface IRuntime
{
    public function extension_loaded($extname);
}
