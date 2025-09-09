<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Drivers;
	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;
    use CzProject\SqlGenerator\NotImplementedException;

    class DropIndex implements IStatement
	{
		private ?string $index;

		public function __construct(?string $index)
		{
			$this->index = $index;
		}


        /**
         * @throws NotImplementedException
         */
        public function toSql(IDriver $driver): string
        {
			if ($this->index === NULL) { // PRIMARY KEY
				if ($driver instanceof Drivers\MysqlDriver) {
					return 'DROP PRIMARY KEY';

				} else {
					throw new NotImplementedException('Drop of primary key is not implemented for driver ' . get_class($driver) . '.');
				}
			}

			return 'DROP INDEX ' . $driver->escapeIdentifier($this->index);
		}
	}
