<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\Driver;
    use CzProject\SqlGenerator\InvalidArgumentException;
    use CzProject\SqlGenerator\Statement;
	use CzProject\SqlGenerator\TableName;

	class Update implements Statement
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
        public function toSql(Driver $driver): string
		{
            try {
                $tableName = Helpers::escapeTableName($this->tableName, $driver);

                $set = Helpers::key_value_to_string(
                    static fn (string $field, mixed $value): string => sprintf('%s = %s', $driver->escapeIdentifier($field), Helpers::formatValue($value, $driver)),
                    $this->data,
                );

                $where = Helpers::key_value_to_string(
                    static fn (string $field, string $value): string => sprintf('%s = %s', $driver->escapeIdentifier($field), Helpers::formatValue($value, $driver)),
                    $this->where,
                    ' AND ',
                );

                return "UPDATE $tableName SET $set WHERE $where;";
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException('Error creating UPDATE query.', previous: $e);
            }
		}
	}
