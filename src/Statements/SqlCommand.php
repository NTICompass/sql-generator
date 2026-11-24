<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Driver;
	use CzProject\SqlGenerator\Statement;

	class SqlCommand implements Statement
	{
		private string $command;

		public function __construct(string $command)
		{
			$this->command = $command;
		}

		public function toSql(Driver $driver): string
        {
			return rtrim($this->command, ';') . ';';
		}
	}
