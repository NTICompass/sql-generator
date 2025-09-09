<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\IDriver;
    use CzProject\SqlGenerator\InvalidArgumentException;
    use CzProject\SqlGenerator\IStatement;
	use CzProject\SqlGenerator\TableName;

	class Update implements IStatement
	{
		private string|TableName $tableName;

		/** @var array<string, mixed> */
		private array $data;

        /** @var array<string, string> */
        private array $where;

		/**
		 * @param array<string, mixed> $data
		 * @param array<string, string> $where
		 */
		public function __construct(string|TableName $tableName, array $data, array $where)
		{
			$this->tableName = Helpers::createTableName($tableName);
			$this->data = $data;
			$this->where = $where;
		}

        /**
         * @throws InvalidArgumentException
         */
        public function toSql(IDriver $driver): string
		{
            try {
                $tableName = Helpers::escapeTableName($this->tableName, $driver);

                $set = implode(',', array_map(
                    static fn (string $field, mixed $value): string => sprintf(
                        '%s=%s',
                        $driver->escapeIdentifier($field),
                        Helpers::formatValue($value, $driver),
                    ),
                    array_keys($this->data),
                    array_values($this->data),
                ));

                $where = implode(' AND ', array_map(
                    static fn (string $field, string $value): string => sprintf(
                        '%s=%s',
                        $driver->escapeIdentifier($field),
                        Helpers::formatValue($value, $driver),
                    ),
                    array_keys($this->where),
                    array_values($this->where),
                ));

                return "UPDATE $tableName SET $set WHERE $where;";
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException('Error creating UPDATE query.', previous: $e);
            }
		}
	}
