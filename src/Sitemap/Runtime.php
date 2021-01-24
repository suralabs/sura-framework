<?php
declare(strict_types=1);
namespace Sura\Sitemap;

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
    public function extension_loaded($extname)
    {
        return extension_loaded($extname);
    }
}
