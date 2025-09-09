<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;

	class DropColumn implements IStatement
	{
		private string $column;

		public function __construct(string $column)
		{
			$this->column = $column;
		}

		public function toSql(IDriver $driver): string
        {
			return 'DROP COLUMN ' . $driver->escapeIdentifier($this->column);
		}
	}
