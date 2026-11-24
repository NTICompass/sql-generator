<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Helpers;
    use CzProject\SqlGenerator\Driver;
	use CzProject\SqlGenerator\Statement;
    use CzProject\SqlGenerator\TableName;

	class RenameTo implements Statement
	{
        private TableName|string $tableName;

        public function __construct(TableName|string $tableName)
        {
            $this->tableName = Helpers::createTableName($tableName);
        }

		public function toSql(Driver $driver): string
		{
			return 'RENAME TO ' . Helpers::escapeTableName($this->tableName, $driver);
		}
	}
