<?php
declare(strict_types=1);

namespace Sura\Libs;

use Sura\Contracts\ModuleInterface;

/**
 *  Module
 *
 */
class Module implements ModuleInterface
{
	/**
	 * @return string|array|null
	 */
	public function user_info(): string|array|null
	{
		return Registry::get('user_info');
	}
	
	/**
	 * @return bool
	 */
	public function logged(): bool|null
	{
		return Registry::get('logged');
	}
	
	/**
	 * @return \Sura\Libs\Db|null
	 */
	public function db(): null|Db
	{
		return Db::getDB();
	}
	
	/**
	 * @return array
	 */
	public function get_langs(): array
	{
		return Langs::get_langs();
	}
}
