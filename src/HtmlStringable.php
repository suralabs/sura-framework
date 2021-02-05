<?php

declare(strict_types=1);

namespace Sura;

interface HtmlStringable
{
	/**
	 * Returns string in HTML format
	 */
	public function __toString(): string;
}


interface_exists(Utils\IHtmlString::class);
