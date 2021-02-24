<?php

declare(strict_types=1);

namespace Sura\Sitemap;

use JetBrains\PhpStorm\Pure;

class FileSystem implements IFileSystem
{
    public function file_get_contents($filepath): bool|string
    {
        return file_get_contents($filepath);
    }

    public function file_put_contents($filepath, $content): bool|int
    {
        return file_put_contents($filepath, $content);
    }

    public function gzopen($filepath, $mode): bool
    {
        return gzopen($filepath, $mode);
    }

    public function gzwrite($file, $content): int
    {
        return gzwrite($file, $content);
    }

    public function gzclose($file): bool
    {
        return gzclose($file);
    }

    #[Pure] public function file_exists($filepath): bool
    {
        return file_exists($filepath);
    }
}
