<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\InvalidArgumentException;
use CzProject\SqlGenerator\PreparedStatement;
use CzProject\SqlGenerator\TableName;

class Delete implements PreparedStatement
{
    private Where $whereData;

    /**
     * @param array<array-key, mixed | Where> $where
     */
    public function __construct(
        private string|TableName $tableName,
        array $where,
        bool $preparedStatements = false,
    ) {
        $this->tableName = Helpers::createTableName($tableName);
        $this->whereData = new Where($where, preparedStatements: $preparedStatements);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function toSql(Driver $driver): string
    {
        try {
            $tableName = Helpers::escapeTableName($this->tableName, $driver);
            $where = $this->whereData->toSql($driver);

            return "DELETE FROM $tableName $where;";
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException('Error creating DELETE query.', previous: $e);
        }
    }

    public function getParams(): array
    {
        return $this->whereData->getParams();
    }
}
