<?php

declare(strict_types=1);

namespace Sura\Caching;

use Sura;


/**
 * Output caching helper.
 */
class OutputHelper
{
	use Sura\SmartObject;

	/** @var array */
	public $dependencies = [];

	/** @var Cache|null */
	private $cache;

	/** @var string */
	private $key;


	public function __construct(Cache $cache, $key)
	{
		$this->cache = $cache;
		$this->key = $key;
		ob_start();
	}


    /**
     * Stops and saves the cache.
     * @param array $dependencies
     * @throws \Throwable
     */
	public function end(array $dependencies = []): void
	{
		if ($this->cache === null) {
			throw new Sura\InvalidStateException('Output cache has already been saved.');
		}
		$this->cache->save($this->key, ob_get_flush(), $dependencies + $this->dependencies);
		$this->cache = null;
	}


	/**
	 * Stops and throws away the output.
	 */
	public function rollback(): void
	{
		ob_end_flush();
		$this->cache = null;
	}
}
