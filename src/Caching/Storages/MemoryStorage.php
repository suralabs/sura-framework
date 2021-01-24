<?php

declare(strict_types=1);

namespace Sura\Caching\Storages;

use Sura;


/**
 * Memory cache storage.
 */
class MemoryStorage implements Sura\Caching\Storage
{
	use Sura\SmartObject;

	/** @var array */
	private array $data = [];


	public function read(string $key): mixed
	{
		return $this->data[$key] ?? null;
	}


	public function lock(string $key): void
	{
	}


	public function write(string $key, $data, array $dependencies): void
	{
		$this->data[$key] = $data;
	}


	public function remove(string $key): void
	{
		unset($this->data[$key]);
	}


	public function clean(array $conditions): void
	{
		if (!empty($conditions[Sura\Caching\Cache::ALL])) {
			$this->data = [];
		}
	}
}
