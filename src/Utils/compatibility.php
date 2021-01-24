<?php

declare(strict_types=1);

namespace Sura\Utils {
	if (false) {
		/** @deprecated use Sura\HtmlStringable */
		interface IHtmlString
		{
		}
	} elseif (!interface_exists(IHtmlString::class)) {
		class_alias(\Sura\HtmlStringable::class, IHtmlString::class);
	}
}

namespace Sura\Localization {
	if (false) {
		/** @deprecated use Sura\Localization\Translator */
		interface ITranslator
		{
		}
	} elseif (!interface_exists(ITranslator::class)) {
		class_alias(Translator::class, ITranslator::class);
	}
}
