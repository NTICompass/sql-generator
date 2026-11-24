<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Helpers;
    use CzProject\SqlGenerator\Driver;
	use CzProject\SqlGenerator\Statement;
    use CzProject\SqlGenerator\TableName;

	class RenameColumnTo implements Statement
	{
        private string $oldName;

        private string $newName;

        public function __construct(string $oldName, string $newName)
        {
            $this->oldName = $oldName;
            $this->newName = $newName;
        }

		public function toSql(Driver $driver): string
		{
            $oldName = $driver->escapeIdentifier($this->oldName);
            $newName = $driver->escapeIdentifier($this->newName);

			return "RENAME COLUMN $oldName TO $newName";
		}
	}
