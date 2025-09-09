<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Drivers;
	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\NotImplementedException;
	use CzProject\SqlGenerator\IStatement;
	use CzProject\SqlGenerator\TableName;

	class RenameTable implements IStatement
	{
		private TableName|string $oldTable;

		private TableName|string $newTable;

		public function __construct(TableName|string $oldTable, TableName|string $newTable)
		{
			$this->oldTable = Helpers::createTableName($oldTable);
			$this->newTable = Helpers::createTableName($newTable);
		}

        /**
         * @throws NotImplementedException
         */
        public function toSql(IDriver $driver): string {
            try {
                $oldTable = Helpers::escapeTableName($this->oldTable, $driver);
                $newTable = Helpers::escapeTableName($this->newTable, $driver);

                return $driver->renameTable($oldTable, $newTable);
            } catch (NotImplementedException $e) {
                // @see http://stackoverflow.com/questions/886786/how-do-i-rename-the-table-name-using-sql-query
                throw new NotImplementedException('Table rename is not implemented for driver ' . get_class($driver) . '.', previous: $e);
            }
		}
	}
