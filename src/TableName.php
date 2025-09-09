<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator;

	class TableName
	{
		/** @var string[] */
		private array $parts;

		/**
		 * @param string ...$parts
		 */
		public function __construct(...$parts)
		{
			$this->parts = $parts;
		}

		public function toString(IDriver $driver): string {
			$res = [];

			foreach ($this->parts as $part) {
				$res[] = $driver->escapeIdentifier($part);
			}

			return implode('.', $res);
		}

		public static function create(string $name): self {
			$parts = explode('.', $name);
			return new self(...$parts);
		}
	}
