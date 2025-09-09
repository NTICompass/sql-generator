<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;

	class DropForeignKey implements IStatement
	{
		private string $foreignKey;

		public function __construct(string $foreignKey)
		{
			$this->foreignKey = $foreignKey;
		}


		public function toSql(IDriver $driver): string
        {
			return 'DROP FOREIGN KEY ' . $driver->escapeIdentifier($this->foreignKey);
		}
	}
