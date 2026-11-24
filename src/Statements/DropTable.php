<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\Driver;
	use CzProject\SqlGenerator\Statement;
	use CzProject\SqlGenerator\TableName;

	class DropTable implements Statement
	{
		private TableName|string $tableName;

		public function __construct(string|TableName $tableName)
		{
			$this->tableName = Helpers::createTableName($tableName);
		}

		public function toSql(Driver $driver): string
        {
			return 'DROP TABLE ' . Helpers::escapeTableName($this->tableName, $driver) . ';';
		}
	}
