<?php

declare(strict_types=1);

namespace Sura\Database;

use Sura;


/**
 * SQL literal value.
 */
class SqlLiteral
{
	use Sura\SmartObject;

	/** @var string */
	private $value;

	/** @var array */
	private $parameters;


	public function __construct(string $value, array $parameters = [])
	{
		$this->value = $value;
		$this->parameters = $parameters;
	}


	public function getParameters(): array
	{
		return $this->parameters;
	}


	public function __toString(): string
	{
		return $this->value;
	}
}
