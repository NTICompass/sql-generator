<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;

	class SqlCommand implements IStatement
	{
		private string $command;

		public function __construct(string $command)
		{
			$this->command = $command;
		}

		public function toSql(IDriver $driver): string
        {
			return rtrim($this->command, ';') . ';';
		}
	}
