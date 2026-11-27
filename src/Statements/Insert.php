<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use Closure;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\InvalidArgumentException;
use CzProject\SqlGenerator\PreparedStatement;
use CzProject\SqlGenerator\TableName;

class Insert implements PreparedStatement
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private string|TableName $tableName,
        private array $data,
        private bool $preparedStatements = false,
        private bool $useSet = false,
    )
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

            if ($this->useSet) {
                if (!$driver->supportsInsertSet()) {
                    throw new InvalidArgumentException('Driver does not support "INSERT INTO ... SET ..." syntax.');
                }

                $insert = (new Where($this->data, 'SET', ', ', preparedStatements: $this->preparedStatements))->toSql($driver);
                return "INSERT INTO $tableName $insert;";
            }
            else {
                $fields = implode(', ', array_map(static fn (string $field) => $driver->escapeIdentifier($field), array_keys($this->data)));
                $values = implode(', ', array_map(fn (mixed $value) => $this->preparedStatements ? '?' : Helpers::formatValue($value, $driver), array_values($this->data)));

                return "INSERT INTO $tableName ($fields) VALUES ($values);";
            }
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException('Error creating INSERT query.', previous: $e);
        }
    }

    public function getParams(): array
    {
        return array_map(Closure::fromCallable([Helpers::class, 'formatValueForPrepared']), array_values($this->data));
    }
}
