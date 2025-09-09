<?php

	declare(strict_types=1);

	namespace Tests;

	use CzProject\SqlGenerator\IDriver;
    use DateTimeImmutable;
    use DateTimeInterface;
    use Exception;

    class DummyDriver implements IDriver
	{
		public function escapeIdentifier(string $value): string
		{
			// @see http://dev.mysql.com/doc/refman/5.0/en/identifiers.html
			// @see http://api.dibiphp.com/2.3.2/source-drivers.DibiMySqlDriver.php.html#307
			return '`' . str_replace('`', '``', $value) . '`';
		}

		public function escapeText(string $value): string
		{
			// https://dev.mysql.com/doc/refman/5.5/en/string-literals.html
			// http://us3.php.net/manual/en/function.mysql-real-escape-string.php#101248
			return '\'' . str_replace(
				['\\', "\0", "\n", "\r", "\t", "'", '"', "\x1a"],
				['\\\\', '\\0', '\\n', '\\r', '\\t', "\\'", '\\"', '\\Z'],
				$value
			) . '\'';
		}

		public function escapeBool(bool $value): string
		{
			return $value ? 'TRUE' : 'FALSE';
		}

		public function escapeDate(DateTimeInterface|string $value): string
		{
            if (!($value instanceof DateTimeInterface)) {
                try {
                    $value = new DateTimeImmutable($value);
                } catch (Exception $e) {
                    return '';
                }
            }

			return $value->format("'Y-m-d'");
		}

		public function escapeDateTime(DateTimeInterface|string $value): string
		{
            if (!($value instanceof DateTimeInterface)) {
                try {
                    $value = new DateTimeImmutable($value);
                } catch (Exception $e) {
                    return '';
                }
            }

			return $value->format("'Y-m-d H:i:s'");
		}
	}
