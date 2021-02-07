<?php


namespace Sura\Contracts;


use Sura\Libs\Settings;

interface ParseInterface
{

    /**
     * @param $source
     * @return string
     */
    public function BBdecodeVideo(string $source): string;

    /**
     * @param $source
     * @param bool $preview
     * @return string
     */
    public function BBvideo(string $source, bool $preview = false): string;

    /**
     * @param $source
     * @return string
     */
    public function BBdecodeLink(string $source): string;

    /**
     * @param string $source
     * @param bool $preview
     * @return string
     */
    public function BBparse(string $source, $preview = false): string;

    /**
     * @param $source
     * @param bool $preview
     * @return string
     */
    public function BBphoto(string $source, $preview = false): string;

    /**
     * @param $source
     * @return string
     */
    public function BBdecodePhoto(string $source): string;

    /**
     * @param $source
     * @return string
     */
    public function BBlink(string $source): string;

    /**
     * @param string $source
     * @return string
     */
    public function BBdecode(string $source): string;
}