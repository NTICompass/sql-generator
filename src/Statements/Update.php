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
		/**
		 * @param array<string, mixed> $data
		 * @param array<array-key, mixed | Where> $where
		 */
		public function __construct(private string|TableName $tableName, private array $data, private array $where)
		{
			$this->tableName = Helpers::createTableName($tableName);
		}

        /**
         * @throws InvalidArgumentException
         */
        public function toSql(Driver $driver): string
		{
            try {
                $tableName = Helpers::escapeTableName($this->tableName, $driver);
                $set = (new Where($this->data, 'SET', ', '))->toSql($driver);
                $where = (new Where($this->where, 'WHERE'))->toSql($driver);

                return "UPDATE $tableName $set $where;";
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException('Error creating UPDATE query.', previous: $e);
            }
		}
	}
