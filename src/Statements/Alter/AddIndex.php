<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;
    use CzProject\SqlGenerator\OutOfRangeException;

    class AddIndex implements IStatement
	{
		private IndexDefinition $definition;

        /**
         * @throws OutOfRangeException
         */
		public function __construct(?string $name, string $type)
		{
			$this->definition = new IndexDefinition($name, $type);
		}

		public function addColumn(string $column, string $order = IndexColumnDefinition::ASC, ?int $length = NULL): static
        {
			$this->definition->addColumn($column, $order, $length);
			return $this;
		}

		public function toSql(IDriver $driver): string
        {
			return 'ADD ' . $this->definition->toSql($driver);
		}
	}
