<?php

declare(strict_types=1);

namespace Sura\Localization;


/**
 * Translator adapter.
 */
interface Translator
{
	/**
	 * Translates the given string.
	 * @param  mixed  $message
	 * @param  mixed  ...$parameters
	 */
	function translate($message, ...$parameters): string;
}


interface_exists(Sura\Localization\ITranslator::class);
