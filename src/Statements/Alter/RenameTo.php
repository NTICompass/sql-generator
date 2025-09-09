<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Helpers;
    use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;
    use CzProject\SqlGenerator\TableName;

	class RenameTo implements IStatement
	{
        private TableName|string $tableName;

        public function __construct(TableName|string $tableName)
        {
            $this->tableName = Helpers::createTableName($tableName);
        }

		public function toSql(IDriver $driver): string
		{
			return 'RENAME TO ' . Helpers::escapeTableName($this->tableName, $driver);
		}
	}
