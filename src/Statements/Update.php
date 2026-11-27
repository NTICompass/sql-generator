<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\InvalidArgumentException;
use CzProject\SqlGenerator\PreparedStatement;
use CzProject\SqlGenerator\TableName;

class Update implements PreparedStatement
{
    private Where $inputData;

    private Where $whereData;

    /**
     * @param array<string, mixed> $data
     * @param array<array-key, mixed | Where> $where
     */
    public function __construct(
        private string|TableName $tableName,
        private array $data,
        private array $where,
        bool $preparedStatements = false,
    )
    {
        $this->tableName = Helpers::createTableName($tableName);
        $this->inputData = new Where($this->data, 'SET', ', ', preparedStatements: $preparedStatements);
        $this->whereData = new Where($this->where, preparedStatements: $preparedStatements);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function toSql(Driver $driver): string
    {
        try {
            $tableName = Helpers::escapeTableName($this->tableName, $driver);
            $set = $this->inputData->toSql($driver);
            $where = $this->whereData->toSql($driver);

            return "UPDATE $tableName $set $where;";
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException('Error creating UPDATE query.', previous: $e);
        }
    }

    public function getParams(): array {
        return array_merge($this->inputData->getParams(), $this->whereData->getParams());
    }
}
