<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Driver;
	use CzProject\SqlGenerator\Statement;
    use CzProject\SqlGenerator\NotImplementedException;
    use CzProject\SqlGenerator\OutOfRangeException;
    use CzProject\SqlGenerator\TableName;

	class AddForeignKey implements Statement
	{
		private ForeignKeyDefinition $definition;


		/**
		 * @param string|string[] $columns
		 * @param string|string[] $targetColumns
		 */
		public function __construct(string $name, array|string $columns, string|TableName $targetTable, array|string $targetColumns)
		{
			$this->definition = new ForeignKeyDefinition($name, $columns, $targetTable, $targetColumns);
		}


        /**
         * @throws OutOfRangeException
         */
		public function setOnUpdateAction(string $onUpdateAction): static
        {
			$this->definition->setOnUpdateAction($onUpdateAction);
			return $this;
		}


        /**
         * @throws OutOfRangeException
         */
		public function setOnDeleteAction(string $onDeleteAction): static
        {
			$this->definition->setOnDeleteAction($onDeleteAction);
			return $this;
		}


        /**
         * @throws NotImplementedException
         */
        public function toSql(Driver $driver): string
        {
            if ($driver->modifyColumn ?? true) {
                return 'ADD ' . $this->definition->toSql($driver);
            }
            else {
                throw new NotImplementedException('Add key is not implemented for driver ' . get_class($driver) . '.');
            }
		}
	}
