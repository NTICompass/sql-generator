<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\IDriver;
    use CzProject\SqlGenerator\InvalidArgumentException;
    use CzProject\SqlGenerator\NotImplementedException;
	use CzProject\SqlGenerator\TableName;

	class RenameColumn extends AlterTable
	{
        private string $oldColumn;

        private string $newColumn;

		public function __construct(TableName|string $tableName, string $oldColumn, string $newColumn)
		{
			parent::__construct($tableName);

            $this->oldColumn = $oldColumn;
            $this->newColumn = $newColumn;
		}

        /**
         * @throws NotImplementedException|InvalidArgumentException
         */
        public function toSql(IDriver $driver): string
        {
            if ($driver->renameColumn ?? true) {
                $this->renameColumn($this->oldColumn, $this->newColumn);

                return parent::toSql($driver);
            }
            else {
                throw new NotImplementedException('Column rename is not implemented for driver ' . get_class($driver) . '.');
            }
		}
	}
