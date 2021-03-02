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
     * Mapper constructor.
     * @param \Traversable $iterator
     * @param callable $callback
     */
	public function __construct(\Traversable $iterator, callable $callback)
	{
		parent::__construct($iterator);
		$this->callback = $callback;
	}

    /**
     * @return mixed
     */
	public function current()
	{
		return ($this->callback)(parent::current(), parent::key());
	}
}
