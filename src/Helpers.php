<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator;

    use DateTimeInterface;

	class Helpers
	{
        /**
         * @throws StaticClassException
         */
        public function __construct()
		{
			throw new StaticClassException('This is static class.');
		}

        /**
         * @throws InvalidArgumentException
         * @see https://api.dibiphp.com/3.0/source-Dibi.Translator.php.html#174
         */
		public static function formatValue(mixed $value, IDriver $driver): string {
			if (is_string($value)) {
				return $driver->escapeText($value);

			} elseif (is_int($value)) {
				return (string)$value;

			} elseif (is_float($value)) {
				return rtrim(rtrim(number_format($value, 10, '.', ''), '0'), '.');

			} elseif (is_bool($value)) {
				return $driver->escapeBool($value);

			} elseif ($value === NULL) {
				return 'NULL';

			} elseif ($value instanceof DateTimeInterface) {
				return $driver->escapeDateTime($value);

			}

			throw new InvalidArgumentException("Unsupported value type.");
		}

		public static function escapeTableName(TableName|string $tableName, IDriver $driver): string {
			if ($tableName instanceof TableName) {
				return $tableName->toString($driver);
			}

			return $driver->escapeIdentifier($tableName);
		}

		public static function createTableName(TableName|string $tableName): TableName|string {
			if (is_string($tableName) && strpos($tableName, '.')) {
				return TableName::create($tableName);
			}

			return $tableName;
		}

		public static function normalizeNewLines(string $s): string {
			return str_replace(["\r\n", "\r"], "\n", $s);
		}
	}
