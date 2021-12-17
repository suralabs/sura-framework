<?php

declare(strict_types=1);

namespace Sura\Utils;

use JetBrains\PhpStorm\Pure;
use Sura;
use Sura\Exception\InvalidArgumentException;


/**
 * Provides objects to work as array.
 */
class ArrayHash extends \stdClass implements \ArrayAccess, \Countable, \IteratorAggregate
{
	/**
	 * Transforms array to ArrayHash.
	 * @param array $array
	 * @param bool $recursive
	 * @return static
	 */
	public static function from(array $array, bool $recursive = true): static
	{
		$obj = new static();
		foreach ($array as $offset => $value) {
			$obj->$offset = $recursive && is_array($value)
				? static::from($value, true)
				: $value;
		}
		return $obj;
	}
	
	
	/**
	 * Returns an iterator over all items.
	 */
	#[Pure] public function getIterator(): \RecursiveArrayIterator
	{
		return new \RecursiveArrayIterator((array) $this);
	}
	
	
	/**
	 * Returns items count.
	 */
	#[Pure] public function count(): int
	{
		return count((array) $this);
	}
	
	
	/**
	 * Replaces or appends a item.
	 * @param string|int $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, mixed $value): void
	{
		if (!is_scalar($offset)) { // prevents null
			throw new InvalidArgumentException(sprintf('Key must be either a string or an integer, %s given.', gettype($offset)));
		}
		$this->$offset = $value;
	}
	
	
	/**
	 * Returns a item.
	 * @param  string|int  $offset
	 * @return mixed
	 */
	public function offsetGet($offset): mixed
    {
		return $this->$offset;
	}


	/**
	 * Determines whether a item exists.
	 * @param string|int $offset
	 * @return bool
	 */
	public function offsetExists($offset): bool
	{
		return isset($this->$offset);
	}
	
	
	/**
	 * Removes the element from this list.
	 * @param  string|int  $offset
	 */
	public function offsetUnset($offset): void
	{
		unset($this->$offset);
	}
}
