<?php

declare(strict_types=1);

namespace Sura\Sitemap;

use JetBrains\PhpStorm\Pure;

/**
 * Class Runtime
 * @package Sura\Sitemap
 */
class Runtime implements IRuntime
{
    /**
     * @param $extname
     * @return bool
     */
    #[Pure] public function extension_loaded($extname): bool
    {
        return extension_loaded($extname);
    }
}
