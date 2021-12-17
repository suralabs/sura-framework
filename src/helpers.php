<?php
declare(strict_types=1);

namespace Sura;

use JetBrains\PhpStorm\Pure;
use App\Application;

if (!function_exists('e')) {
	/**
	 * Escape HTML entities in a string.
	 *
	 * @param string $value
	 * @return string
	 */
	#[Pure] function e(string $value): string
	{
		return html_entity_decode($value);
	}
}

/**
 * Get the available container instance.
 *
 * @param string|null $abstract
 * @param array $parameters
 * @return mixed
 */
function app($abstract = null, array $parameters = []): mixed
{
	if (is_null($abstract)) {
		return Application::getInstance();
	}
	
	return Application::getInstance()->make($abstract, $parameters);
}

/**
 * Resolve a service from the container.
 *
 * @param string $name
 * @return mixed
 */
function resolve(string $name): mixed
{
	return app($name);
}
