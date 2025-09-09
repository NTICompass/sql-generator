<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;
	use CzProject\SqlGenerator\TableName;

	class DropTable implements IStatement
	{
		private TableName|string $tableName;

		public function __construct(string|TableName $tableName)
		{
			$this->tableName = Helpers::createTableName($tableName);
		}

		public function toSql(IDriver $driver): string {
			return 'DROP TABLE ' . Helpers::escapeTableName($this->tableName, $driver) . ';';
		}
	}
