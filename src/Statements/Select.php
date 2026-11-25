<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use Closure;
use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\InvalidArgumentException;
use CzProject\SqlGenerator\Statement;

require_once __DIR__ . '/../../vendor/autoload.php'; // We're using `array_is_list` which is PHP 8.1+

/**
 * @phpstan-type Join array{0: string | self, 1: array<string, mixed> | list<string>, 2?: 'inner' | 'left'} | array{0: string | self, 1: array<string, mixed> | list<string>}
 */
class Select implements Statement
{
    /**
     * @param array<int | string, string | self> $fields
     * @param array<int | string, string | self> $tables
     * @param array<int | string, Join> $join
     * @param array<string, mixed> $where
     * @param list<string> $group
     * @param array<string, mixed> $having
     * @param list<array{0: string} | array{0: string, 1?: 'asc' | 'desc'}> $order
     * @param int $limit
     * @param int $offset
     * @param bool $distinct
     */
    public function __construct(
        private array $fields = ['*'],
        private array $tables = [],
        private array $join = [],
        private array $where = [],
        private array $group = [],
        private array $having = [],
        private array $order = [],
        private int $limit = 0,
        private int $offset = 0,
        private bool $distinct = false,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function toSql(Driver $driver): string
    {
        $select = $this->distinct ? 'SELECT DISTINCT' : 'SELECT';

        $fields = Helpers::key_value_to_string(
            static fn (int | string $alias, string | self $value): string =>
                (is_string($value) ? $value : '(' . $value->toSql($driver) . ')') .
                (!is_int($alias) ? ' AS ' . $driver->escapeIdentifier($alias) : ''),
            $this->fields,
        );

        $fromData = Helpers::key_value_to_string(
            static fn (int | string $alias, string | self $value): string =>
                (is_string($value) ? Helpers::escapeTableName(Helpers::createTableName($value), $driver) : '(' . $value->toSql($driver) . ')') .
                (!is_int($alias) ? ' AS ' . $driver->escapeIdentifier($alias) : ''),
            $this->tables,
        );
        $from = $fromData !== '' ? "FROM $fromData" : '';

        $join = Helpers::key_value_to_string(
            /**
             * @param Join $value
             * @throws InvalidArgumentException
             */
            static fn (int | string $alias, array $value): string => trim(
                strtoupper($value[2] ?? '') . ' JOIN ' .
                (is_string($value[0]) ? Helpers::escapeTableName(Helpers::createTableName($value[0]), $driver) : '(' . $value[0]->toSql($driver) . ')') .
                (!is_int($alias) ? ' AS ' . $driver->escapeIdentifier($alias) : '').
                (array_is_list($value[1])
                    ? ' USING(' . implode(', ', array_map(Closure::fromCallable([$driver, 'escapeIdentifier']), $value[1])) . ')'
                    : ' ON ' . Helpers::key_value_to_string(
                        static fn (string $field, mixed $value): string => sprintf('%s = %s', $driver->escapeIdentifier($field), Helpers::formatValue($value, $driver)),
                        $value[1],
                        ' AND ',
                    )
                ),
            ),
            $this->join,
        );

        $whereData = Helpers::key_value_to_string(
            static fn (string $field, mixed $value): string => sprintf('%s = %s', $driver->escapeIdentifier($field), Helpers::formatValue($value, $driver)),
            $this->where,
            ' AND ',
        );
        $where = $whereData !== '' ? "WHERE $whereData" : '';

        $groupData = implode(', ', array_map(static fn (string $value): string => Helpers::escapeTableName(Helpers::createTableName($value), $driver), $this->group));
        $group = $groupData !== '' ? "GROUP BY $groupData" : '';

        $havingData = Helpers::key_value_to_string(
            static fn (string $field, mixed $value): string => sprintf('%s = %s', $driver->escapeIdentifier($field), Helpers::formatValue($value, $driver)),
            $this->having,
            ' AND ',
        );
        $having = $havingData !== '' ? "HAVING $havingData" : '';

        $orderData = implode(', ', array_map(
            /**
             * @param array{0: string, 1?: 'asc' | 'desc'} $value
             */
            static fn (array $value): string => trim(Helpers::escapeTableName(Helpers::createTableName($value[0]), $driver) . ' ' . strtoupper($value[1] ?? '')),
            $this->order,
        ));
        $order = $orderData !== '' ? "ORDER BY $orderData" : '';

        $limit = $this->limit > 0 ? "LIMIT {$this->limit}" : '';
        $offset = $this->offset > 0 ? "OFFSET {$this->offset}" : '';

        return trim("$select $fields $from $join $where $group $having $order $limit $offset") . ';';
    }
}
