<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Drivers;
	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\NotImplementedException;
	use CzProject\SqlGenerator\IStatement;
	use CzProject\SqlGenerator\TableName;

	class RenameColumn implements IStatement
	{
		private TableName|string $tableName;

        private string $oldColumn;

        private string $newColumn;

		public function __construct(TableName|string $tableName, string $oldColumn, string $newColumn)
		{
			$this->tableName = Helpers::createTableName($tableName);
            $this->oldColumn = $oldColumn;
            $this->newColumn = $newColumn;
		}

        /**
         * @throws NotImplementedException
         */
        public function toSql(IDriver $driver): string {
            if ($driver->renameColumn ?? true) {
                $tableName = Helpers::escapeTableName($this->tableName, $driver);
                $oldColumn = $driver->escapeIdentifier($this->oldColumn);
                $newColumn = $driver->escapeIdentifier($this->newColumn);

                // Works with both SQLite and MySQL/MariaDB
                return "ALTER TABLE $tableName RENAME COLUMN $oldColumn TO $newColumn;";
            }
            else {
                // @see http://stackoverflow.com/questions/886786/how-do-i-rename-the-table-name-using-sql-query
                throw new NotImplementedException('Column rename is not implemented for driver ' . get_class($driver) . '.');
            }
		}
	}
