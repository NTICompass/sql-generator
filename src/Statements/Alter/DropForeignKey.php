<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;
    use CzProject\SqlGenerator\NotImplementedException;

    class DropForeignKey implements IStatement
	{
		private string $foreignKey;

		public function __construct(string $foreignKey)
		{
			$this->foreignKey = $foreignKey;
		}


        /**
         * @throws NotImplementedException
         */
        public function toSql(IDriver $driver): string
        {
            if ($driver->modifyColumn ?? true) {
                return 'DROP FOREIGN KEY ' . $driver->escapeIdentifier($this->foreignKey);
            }
            else {
                throw new NotImplementedException('Drop key is not implemented for driver ' . get_class($driver) . '.');
            }
		}
	}
