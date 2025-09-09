<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;
    use CzProject\SqlGenerator\OutOfRangeException;
    use CzProject\SqlGenerator\TableName;

	class AddForeignKey implements IStatement
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


		public function toSql(IDriver $driver): string
        {
			return 'ADD ' . $this->definition->toSql($driver);
		}
	}
