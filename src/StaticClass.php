<?php

declare(strict_types=1);

namespace Sura;


use Error;

/**
 * Static class.
 */
trait StaticClass
{
	/** @throws Error */
	final public function __construct()
	{
		throw new Error('Class ' . static::class . ' is static and cannot be instantiated.');
	}


    /**
     * Call to undefined static method.
     * @param string $name
     * @param array $args
     * @return void
     */
	public static function __callStatic(string $name, array $args)
	{
		Utils\ObjectHelpers::strictStaticCall(static::class, $name);
	}
}

