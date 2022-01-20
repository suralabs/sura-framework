<?php

declare(strict_types=1);

namespace Sura\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Sura\Utils\FileSystem;
use function Sura\resolve;

/**
 * $Header$
 *
 * @version $Revision$
 * @package Log
 */


/**
 * The Log:: class implements both an abstraction for various logging
 * mechanisms and the Subject end of a Subject-Observer pattern.
 *
 * @package Log
 */
class Log{

    private Logger $log;

    public string $log_dir;
    public string $log_file = 'warning.log';
    public int $level = Logger::WARNING;

    public function __construct()
    {
        $this->log_dir = resolve('app')->get('path').'/log/';

        if (!is_dir($this->log_dir)){
            FileSystem::createDir($this->log_dir);
        }

        $this->log = new Logger('warning');
        $this->log->pushHandler(new StreamHandler($this->log_dir.$this->log_file, $this->level));
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = [])
    {
        $this->log->warning($message, $context);
    }

}