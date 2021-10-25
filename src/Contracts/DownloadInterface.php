<?php


namespace Sura\Contracts;


use Sura\Libs\Download;

interface DownloadInterface
{


    /**
     * @param $filename
     * @param int $range
     * @return bool
     */
    function _download(string $filename, int $range = 0): bool;

    /**
     * @param $path
     * @param string $name
     * @param int $resume
     * @param int $max_speed
     */
    function download(string $path, $name = "", $resume = 0, $max_speed = 0);

    /**
     *
     */
    function download_file();
}