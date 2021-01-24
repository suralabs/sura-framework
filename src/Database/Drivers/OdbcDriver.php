<?php

declare(strict_types=1);

namespace Sura\Database\Drivers;

use Sura;


/**
 * Supplemental ODBC database driver.
 */
class OdbcDriver implements Sura\Database\Driver
{
	use Sura\SmartObject;

	public function initialize(Sura\Database\Connection $connection, array $options): void
	{
	}


	public function convertException(\PDOException $e): Sura\Database\DriverException
	{
		return Sura\Database\DriverException::from($e);
	}


	/********************* SQL ****************d*g**/


	public function delimite(string $name): string
	{
		return '[' . str_replace(['[', ']'], ['[[', ']]'], $name) . ']';
	}


	public function formatDateTime(\DateTimeInterface $value): string
	{
		return $value->format('#m/d/Y H:i:s#');
	}


	public function formatDateInterval(\DateInterval $value): string
	{
		throw new Sura\Exception\NotSupportedException;
	}


	public function formatLike(string $value, int $pos): string
	{
		$value = strtr($value, ["'" => "''", '%' => '[%]', '_' => '[_]', '[' => '[[]']);
		return ($pos <= 0 ? "'%" : "'") . $value . ($pos >= 0 ? "%'" : "'");
	}


	public function applyLimit(string &$sql, ?int $limit, ?int $offset): void
	{
		if ($offset) {
			throw new Sura\Exception\NotSupportedException('Offset is not supported by this database.');

		} elseif ($limit < 0) {
			throw new Sura\Exception\InvalidArgumentException('Negative offset or limit.');

		} elseif ($limit !== null) {
			$sql = preg_replace('#^\s*(SELECT(\s+DISTINCT|\s+ALL)?|UPDATE|DELETE)#i', '$0 TOP ' . $limit, $sql, 1, $count);
			if (!$count) {
				throw new Sura\Exception\InvalidArgumentException('SQL query must begin with SELECT, UPDATE or DELETE command.');
			}
		}
	}


	/********************* reflection ****************d*g**/


	public function getTables(): array
	{
		throw new \Sura\Exception\NotImplementedException;
	}


	public function getColumns(string $table): array
	{
		throw new \Sura\Exception\NotImplementedException;
	}


	public function getIndexes(string $table): array
	{
		throw new \Sura\Exception\NotImplementedException;
	}


	public function getForeignKeys(string $table): array
	{
		throw new \Sura\Exception\NotImplementedException;
	}


	public function getColumnTypes(\PDOStatement $statement): array
	{
		return [];
	}


	public function isSupported(string $item): bool
	{
		return $item === self::SUPPORT_SUBSELECT;
	}
}
