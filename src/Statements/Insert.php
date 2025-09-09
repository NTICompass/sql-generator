<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\IDriver;
    use CzProject\SqlGenerator\InvalidArgumentException;
    use CzProject\SqlGenerator\IStatement;
	use CzProject\SqlGenerator\TableName;

	class Insert implements IStatement
	{
		private TableName|string $tableName;

		/** @var array<string, mixed> */
		private array $data;

		/**
		 * @param array<string, mixed> $data
		 */
		public function __construct(string|TableName $tableName, array $data)
		{
			$this->tableName = Helpers::createTableName($tableName);
			$this->data = $data;
		}

        /**
         * @throws InvalidArgumentException
         */
        public function toSql(IDriver $driver): string
        {
            try {
                $tableName = Helpers::escapeTableName($this->tableName, $driver);
                $fields = implode(',', array_map(static fn (string $field) => $driver->escapeIdentifier($field), array_keys($this->data)));
                $values = implode(',', array_map(static fn (mixed $value) => Helpers::formatValue($value, $driver), array_values($this->data)));

                return "INSERT INTO $tableName($fields) VALUES ($values);";
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException('Error creating UPDATE query.', previous: $e);
            }
		}
	}
