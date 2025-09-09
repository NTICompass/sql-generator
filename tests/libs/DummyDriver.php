<?php

	declare(strict_types=1);

	namespace Tests;

	use CzProject\SqlGenerator\Drivers\DateParserTrait;
	use CzProject\SqlGenerator\IDriver;
    use DateTimeInterface;

    class DummyDriver implements IDriver
	{
        use DateParserTrait;

        /**
         * @var string[]
         */
        private array $transaction = [
            'start' => 'BEGIN;',
            'commit' => 'COMMIT;',
            'rollback' => 'ROLLBACK',
        ];

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
            return $this->dateFormat($value, "'Y-m-d'");
		}

		public function escapeDateTime(DateTimeInterface|string $value): string
		{
            return $this->dateFormat($value, "'Y-m-d H:i:s'");
		}

        public function transaction(string $action): string
        {
            return $this->transaction[$action];
        }
	}
