<?php

declare(strict_types=1);

namespace Sura\Iterators;


/**
 * Applies the callback to the elements of the inner iterator.
 */
class Mapper extends \IteratorIterator
{
	/** @var callable */
	private $callback;


    /**
     * Create an iterator from anything that is traversable
     * @link https://php.net/manual/en/iteratoriterator.construct.php
     * @param \Traversable $iterator
     * @param callable $callback
     */
    public function __construct(\Traversable $iterator, callable $callback)
	{
		parent::__construct($iterator);
		$this->callback = $callback;
	}


    /**
     * Get the current value
     * @link https://php.net/manual/en/iteratoriterator.current.php
     * @return mixed The value of the current element.
     */
    public function current(): mixed
    {
		return ($this->callback)(parent::current(), parent::key());
	}
}
