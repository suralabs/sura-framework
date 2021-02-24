<?php

declare(strict_types=1);

namespace Sura\Utils;

use DateTimeInterface;
use JetBrains\PhpStorm\Pure;
use ReflectionException;
use Sura;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;


/**
 * Finder allows searching through directory trees using iterator.
 *
 * <code>
 * Finder::findFiles('*.php')
 *     ->size('> 10kB')
 *     ->from('.')
 *     ->exclude('temp');
 * </code>
 */
class Finder implements \IteratorAggregate, \Countable
{
	use Sura\SmartObject;

	/** @var callable  extension methods */
	private static $extMethods = [];

	/** @var array */
	private array $paths = [];

	/** @var array of filters */
	private array $groups = [];

	/** @var array filter for recursive traversing */
	private array $exclude = [];

	/** @var int */
	private int $order = RecursiveIteratorIterator::SELF_FIRST;

	/** @var int */
	private int $maxDepth = -1;

	/** @var array */
	private array $cursor;


	/**
	 * Begins search for files matching mask and all directories.
	 * @param  string|string[]  $masks
	 * @return static
	 */
	public static function find(...$masks): self
	{
		$masks = $masks && is_array($masks[0]) ? $masks[0] : $masks;
		return (new Finder)->select($masks, 'isDir')->select($masks, 'isFile');
	}


	/**
	 * Begins search for files matching mask.
	 * @param  string|string[]  $masks
	 * @return static
	 */
	public static function findFiles(...$masks): self
	{
		$masks = $masks && is_array($masks[0]) ? $masks[0] : $masks;
		return (new Finder)->select($masks, 'isFile');
	}


	/**
	 * Begins search for directories matching mask.
	 * @param  string|string[]  $masks
	 * @return static
	 */
	public static function findDirectories(...$masks): self
	{
		$masks = $masks && is_array($masks[0]) ? $masks[0] : $masks;
		return (new Finder)->select($masks, 'isDir');
	}


    /**
     * Creates filtering group by mask & type selector.
     * @param array $masks
     * @param string $type
     * @return static
     */
	private function select(array $masks, string $type): self
	{
		$this->cursor = &$this->groups[];
		$pattern = self::buildPattern($masks);
		$this->filter(function (RecursiveDirectoryIterator $file) use ($type, $pattern): bool {
			return !$file->isDot()
				&& $file->$type()
				&& (!$pattern || preg_match($pattern, '/' . str_replace('\\', '/', $file->getSubPathName())));
		});
		return $this;
	}


	/**
	 * Searches in the given folder(s).
	 * @param  string|string[]  $paths
	 * @return static
	 */
	public function in(...$paths): self
	{
		$this->maxDepth = 0;
		return $this->from(...$paths);
	}


	/**
	 * Searches recursively from the given folder(s).
	 * @param  string|string[]  $paths
	 * @return static
	 */
	public function from(...$paths): self
	{
		if ($this->paths) {
			throw new Sura\Exception\InvalidStateException('Directory to search has already been specified.');
		}
		$this->paths = is_array($paths[0]) ? $paths[0] : $paths;
		$this->cursor = &$this->exclude;
		return $this;
	}


	/**
	 * Shows folder content prior to the folder.
	 * @return static
	 */
	public function childFirst(): self
	{
		$this->order = RecursiveIteratorIterator::CHILD_FIRST;
		return $this;
	}


    /**
     * Converts Finder pattern to regular expression.
     * @param array $masks
     * @return string|null
     */
	private static function buildPattern(array $masks): ?string
	{
		$pattern = [];
		foreach ($masks as $mask) {
			$mask = rtrim(str_replace('\\', '/', $mask), '/');
			$prefix = '';
            if ($mask === '') {
                continue;

            }

            if ($mask === '*') {
                return null;
            }

            if ($mask[0] === '/') { // absolute fixing
                $mask = ltrim($mask, '/');
                $prefix = '(?<=^/)';
            }
            $pattern[] = $prefix . strtr(preg_quote($mask, '#'),
				['\*\*' => '.*', '\*' => '[^/]*', '\?' => '[^/]', '\[\!' => '[^', '\[' => '[', '\]' => ']', '\-' => '-']);
		}
		return $pattern ? '#/(' . implode('|', $pattern) . ')$#Di' : null;
	}


	/********************* iterator generator ****************d*g**/


	/**
	 * Get the number of found files and/or directories.
	 */
	public function count(): int
	{
		return iterator_count($this->getIterator());
	}


	/**
	 * Returns iterator.
	 */
	public function getIterator(): \Iterator
	{
        if (!$this->paths) {
            throw new Sura\Exception\InvalidStateException('Call in() or from() to specify directory to search.');

        }

        if (count($this->paths) === 1) {
            return $this->buildIterator((string) $this->paths[0]);

        }

        $iterator = new \AppendIterator();
        foreach ($this->paths as $path) {
            $iterator->append($this->buildIterator((string) $path));
        }
        return $iterator;
    }


    /**
     * Returns per-path iterator.
     * @param string $path
     * @return \Iterator
     */
	private function buildIterator(string $path): \Iterator
	{
		$iterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::FOLLOW_SYMLINKS);

		if ($this->exclude) {
			$iterator = new \RecursiveCallbackFilterIterator($iterator, function ($foo, $bar, RecursiveDirectoryIterator $file): bool {
				if (!$file->isDot() && !$file->isFile()) {
					foreach ($this->exclude as $filter) {
						if (!$filter($file)) {
							return false;
						}
					}
				}
				return true;
			});
		}

		if ($this->maxDepth !== 0) {
			$iterator = new RecursiveIteratorIterator($iterator, $this->order);
			$iterator->setMaxDepth($this->maxDepth);
		}

		$iterator = new \CallbackFilterIterator($iterator, function ($foo, $bar, \Iterator $file): bool {
			while ($file instanceof \OuterIterator) {
				$file = $file->getInnerIterator();
			}

			foreach ($this->groups as $filters) {
				foreach ($filters as $filter) {
					if (!$filter($file)) {
						continue 2;
					}
				}
				return true;
			}
			return false;
		});

		return $iterator;
	}


	/********************* filtering ****************d*g**/


	/**
	 * Restricts the search using mask.
	 * Excludes directories from recursive traversing.
	 * @param  string|string[]  $masks
	 * @return static
	 */
	public function exclude(...$masks): self
	{
		$masks = $masks && is_array($masks[0]) ? $masks[0] : $masks;
		$pattern = self::buildPattern($masks);
		if ($pattern) {
			$this->filter(function (RecursiveDirectoryIterator $file) use ($pattern): bool {
				return !preg_match($pattern, '/' . strtr($file->getSubPathName(), '\\', '/'));
			});
		}
		return $this;
	}


	/**
	 * Restricts the search using callback.
	 * @param  callable  $callback  function (RecursiveDirectoryIterator $file): bool
	 * @return static
	 */
	public function filter(callable $callback): self
	{
		$this->cursor[] = $callback;
		return $this;
	}


    /**
     * Limits recursion level.
     * @param int $depth
     * @return static
     */
	public function limitDepth(int $depth): self
	{
		$this->maxDepth = $depth;
		return $this;
	}


    /**
     * Restricts the search by size.
     * @param string $operator "[operator] [size] [unit]" example: >=10kB
     * @param int $size
     * @return static
     */
	public function size(string $operator, int $size = 0): self
	{
		if (func_num_args() === 1) { // in $operator is predicate
			if (!preg_match('#^(?:([=<>!]=?|<>)\s*)?((?:\d*\.)?\d+)\s*(K|M|G|)B?$#Di', $operator, $matches)) {
				throw new Sura\Exception\InvalidArgumentException('Invalid size predicate format.');
			}
			[, $operator, $size, $unit] = $matches;
			static $units = ['' => 1, 'k' => 1e3, 'm' => 1e6, 'g' => 1e9];
			$size *= $units[strtolower($unit)];
			$operator = $operator ?: '=';
		}
		return $this->filter(function (RecursiveDirectoryIterator $file) use ($operator, $size): bool {
			return self::compare($file->getSize(), $operator, $size);
		});
	}


    /**
     * Restricts the search by modified time.
     * @param string $operator "[operator] [date]" example: >1978-01-23
     * @param string|int|DateTimeInterface $date
     * @return static
     * @throws \Exception
     */
	public function date(string $operator, $date = null): self
	{
		if (func_num_args() === 1) { // in $operator is predicate
			if (!preg_match('#^(?:([=<>!]=?|<>)\s*)?(.+)$#Di', $operator, $matches)) {
				throw new Sura\Exception\InvalidArgumentException('Invalid date predicate format.');
			}
			[, $operator, $date] = $matches;
			$operator = $operator ?: '=';
		}
		$date = DateTime::from($date)->format('U');
		return $this->filter(function (RecursiveDirectoryIterator $file) use ($operator, $date): bool {
			return self::compare($file->getMTime(), $operator, $date);
		});
	}


    /**
     * Compares two values.
     * @param $l
     * @param string $operator
     * @param $r
     * @return bool
     */
	#[Pure] public static function compare($l, string $operator, $r): bool
	{
        return match ($operator) {
            '>' => $l > $r,
            '>=' => $l >= $r,
            '<' => $l < $r,
            '<=' => $l <= $r,
            '=', '==' => $l == $r,
            '!', '!=', '<>' => $l != $r,
            default => throw new Sura\Exception\InvalidArgumentException("Unknown operator $operator."),
        };
	}


    /**
     * @param string $name
     * @param array $args
     * @return mixed
     * @throws ReflectionException
     */
	public function __call(string $name, array $args): mixed
    {
		return isset(self::$extMethods[$name])
			? (self::$extMethods[$name])($this, ...$args)
			: Sura\Utils\ObjectHelpers::strictCall(get_class($this), $name, array_keys(self::$extMethods));
	}


	public static function extensionMethod(string $name, callable $callback): void
	{
		self::$extMethods[$name] = $callback;
	}
}
