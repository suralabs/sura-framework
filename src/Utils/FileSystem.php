<?php

declare(strict_types=1);

namespace Sura\Utils;

use JetBrains\PhpStorm\Pure;
use Sura;
use Sura\Exception\InvalidStateException;
use Sura\Exception\IOException;


/**
 * File system tool.
 */
final class FileSystem
{
	use Sura\StaticClass;

    /**
     * Creates a directory if it doesn't exist.
     * @throws IOException  on error occurred
     * @param string $dir
     * @param int $mode
     */
	public static function createDir(string $dir, int $mode = 0777): void
	{
		if (!is_dir($dir) && !mkdir($dir, $mode, true) && !is_dir($dir)) { // @ - dir may already exist
			throw new IOException("Unable to create directory '$dir' with mode " . decoct($mode) . '. ' . Helpers::getLastError());
		}
	}


    /**
     * Copies a file or a directory. Overwrites existing files and directories by default.
     * @param string $origin
     * @param string $target
     * @param bool $overwrite
     * @throws IOException  on error occurred
     * @throws InvalidStateException  if $overwrite is set to false and destination already exists
     */
	public static function copy(string $origin, string $target, bool $overwrite = true): void
	{
		if (stream_is_local($origin) && !file_exists($origin)) {
			throw new IOException("File or directory '$origin' not found.");

		} elseif (!$overwrite && is_file($target)) {
			throw new InvalidStateException("File or directory '$target' already exists.");

		} elseif (is_dir($origin)) {
			self::createDir($target);
			foreach (new \FilesystemIterator($target) as $item) {
				self::delete($item->getPathname());
			}
			foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($origin, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
				if ($item->isDir()) {
					self::createDir($target . '/' . $iterator->getSubPathName());
				} else {
					self::copy($item->getPathname(), $target . '/' . $iterator->getSubPathName());
				}
			}

		} else {
			self::createDir(dirname($target));
			if (
				($s = @fopen($origin, 'rb'))
				&& ($d = @fopen($target, 'wb'))
				&& @stream_copy_to_stream($s, $d) === false
			) { // @ is escalated to exception
				throw new IOException("Unable to copy file '$origin' to '$target'. " . Helpers::getLastError());
			}
		}
	}


    /**
     * Deletes a file or directory if exists.
     * @param string $path
     * @throws IOException  on error occurred
     */
	public static function delete(string $path): void
	{
		if (is_file($path) || is_link($path)) {
			$func = DIRECTORY_SEPARATOR === '\\' && is_dir($path) ? 'rmdir' : 'unlink';
			if (!$func($path)) { // @ is escalated to exception
				throw new IOException("Unable to delete '$path'. " . Helpers::getLastError());
			}

		} elseif (is_dir($path)) {
			foreach (new \FilesystemIterator($path) as $item) {
				self::delete($item->getPathname());
			}
			if (!rmdir($path)) { // @ is escalated to exception
				throw new IOException("Unable to delete directory '$path'. " . Helpers::getLastError());
			}
		}
	}


    /**
     * Renames or moves a file or a directory. Overwrites existing files and directories by default.
     * @param string $origin
     * @param string $target
     * @param bool $overwrite
     * @throws IOException  on error occurred
     * @throws InvalidStateException  if $overwrite is set to false and destination already exists
     */
	public static function rename(string $origin, string $target, bool $overwrite = true): void
	{
		if (!$overwrite && file_exists($target)) {
			throw new InvalidStateException("File or directory '$target' already exists.");

		} elseif (!file_exists($origin)) {
			throw new IOException("File or directory '$origin' not found.");

		} else {
			self::createDir(dirname($target));
			if (realpath($origin) !== realpath($target)) {
				self::delete($target);
			}
			if (!rename($origin, $target)) { // @ is escalated to exception
				throw new IOException("Unable to rename file or directory '$origin' to '$target'. " . Helpers::getLastError());
			}
		}
	}


    /**
     * Reads the content of a file.
     * @param string $file
     * @return string
     * @throws IOException  on error occurred
     */
	public static function read(string $file): string
	{
	    if (is_file($file)){
            $content = file_get_contents($file); // @ is escalated to exception
            if ($content === false) {
                throw new IOException("Unable to read file '$file'. " . Helpers::getLastError());
            }
            return $content;
        }
	    return throw new IOException("Unable to read file '$file'. " . Helpers::getLastError());

	}


    /**
     * Writes the string to a file.
     * @param string $file
     * @param string $content
     * @param int|null $mode
     * @throws IOException  on error occurred
     */
	public static function write(string $file, string $content, ?int $mode = 0666): void
	{
		self::createDir(dirname($file));
		if (file_put_contents($file, $content) === false) { // @ is escalated to exception
			throw new IOException("Unable to write file '$file'. " . Helpers::getLastError());
		}
		if ($mode !== null && !chmod($file, $mode)) { // @ is escalated to exception
			throw new IOException("Unable to chmod file '$file' to mode " . decoct($mode) . '. ' . Helpers::getLastError());
		}
	}


    /**
     * Determines if the path is absolute.
     * @param string $path
     * @return bool
     */
	public static function isAbsolute(string $path): bool
	{
		return (bool) preg_match('#([a-z]:)?[/\\\\]|[a-z][a-z0-9+.-]*://#Ai', $path);
	}


    /**
     * Normalizes `..` and `.` and directory separators in path.
     * @param string $path
     * @return string
     */
	public static function normalizePath(string $path): string
	{
		$parts = $path === '' ? [] : preg_split('~[/\\\\]+~', $path);
		$res = [];
		foreach ($parts as $part) {
			if ($part === '..' && $res && end($res) !== '..' && end($res) !== '') {
				array_pop($res);
			} elseif ($part !== '.') {
				$res[] = $part;
			}
		}
		return $res === ['']
			? DIRECTORY_SEPARATOR
			: implode(DIRECTORY_SEPARATOR, $res);
	}


    /**
     * Joins all segments of the path and normalizes the result.
     * @param string ...$paths
     * @return string
     */
	public static function joinPaths(string ...$paths): string
	{
		return self::normalizePath(implode('/', $paths));
	}

    /**
     * @param $file_size
     * @return string
     */
    #[Pure] public static function formatSize($file_size): string
    {
        if($file_size >= 1073741824){
            $file_size = round($file_size / 1073741824 * 100 ) / 100 ." Гб";
        } elseif($file_size >= 1048576){
            $file_size = round($file_size / 1048576 * 100 ) / 100 ." Мб";
        } elseif($file_size >= 1024){
            $file_size = round($file_size / 1024 * 100 ) / 100 ." Кб";
        } else {
            $file_size = $file_size." б";
        }
        return $file_size;
    }
}
