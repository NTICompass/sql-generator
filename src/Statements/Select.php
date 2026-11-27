<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use Closure;
use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\InvalidArgumentException;
use CzProject\SqlGenerator\PreparedStatement;
use CzProject\SqlGenerator\TableName;

require_once __DIR__ . '/../../vendor/autoload.php'; // We're using `array_is_list` which is PHP 8.1+

/**
 * @phpstan-type Join array{0: string | TableName | self, 1: array<array-key, mixed | Where> | list<string>, 2?: 'inner' | 'left' | null}
 * @phpstan-type Order array{0: string, 1?: 'asc' | 'desc' | null}
 */
class Select implements PreparedStatement
{
    private ?Where $whereData;

    /**
     * @param array<array-key, string | self> $fields
     * @param array<array-key, string | TableName | self> $tables
     * @param Join[] $join
     * @param array<array-key, mixed | Where> $where
     * @param list<string> $group
     * @param array<array-key, mixed | Where> $having
     * @param list<Order> $order
     * @param int $limit
     * @param int $offset
     * @param bool $distinct
     * @param bool $preparedStatements
     */
    public function __construct(
        private array $fields = ['*'],
        private array $tables = [],
        private array $join = [],
        array $where = [],
        private array $group = [],
        private array $having = [],
        private array $order = [],
        public int $limit = 0,
        public int $offset = 0,
        public bool $distinct = false,
        bool $preparedStatements = false,
    )
    {
        $this->whereData = $where !== [] ? new Where($where, preparedStatements: $preparedStatements) : null;
    }

    /**
     * @param string|TableName $table
     * @param array<array-key, mixed | Where> | list<string> $on
     * @param 'inner' | 'left' | null $type
     * @return $this
     */
    public function addJoin(string|TableName $table, array $on, ?string $type = null): self {
        $this->join[] = [$table, $on, $type];
        return $this;
    }

    public function addGroup(string $group): self {
        $this->group[] = $group;
        return $this;
    }

    /**
     * @param string $order
     * @param 'asc' | 'desc' | null $dir
     * @return $this
     */
    public function addOrder(string $order, ?string $dir = null): self {
        $this->order[] = [$order, $dir];
        return $this;
    }

    /**
     * @param array-key $field
     * @param mixed | Where $value
     * @return $this
     */
    public function addHaving(string|int $field, mixed $value): self {
        $this->having[$field] = $value;
        return $this;
    }

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
            static fn (int | string $alias, string | TableName | self $value): string =>
                ((is_string($value) || $value instanceof TableName) ? Helpers::escapeTableName(Helpers::createTableName($value), $driver) : '(' . $value->toSql($driver) . ')') .
                (!is_int($alias) ? ' AS ' . $driver->escapeIdentifier($alias) : ''),
            $this->tables,
        );
        $from = $fromData !== '' ? "FROM $fromData" : '';

        $join = Helpers::key_value_to_string(
            /**
             * @param Join $value
             * @throws InvalidArgumentException
             */
            static function (int | string $alias, array $value) use ($driver): string {
                $type = strtoupper($value[2] ?? '') . ' JOIN';
                $table = (is_string($value[0]) || $value[0] instanceof TableName) ? Helpers::escapeTableName(Helpers::createTableName($value[0]), $driver) : '(' . $value[0]->toSql($driver) . ')';
                $as = !is_int($alias) ? ' AS ' . $driver->escapeIdentifier($alias) : '';

                if (array_is_list($value[1])) {
                    // @phpstan-ignore argument.type (`array_map()` expects `callable(mixed): mixed`, not `Closure(string): string`)
                    $clause = ' USING(' . implode(', ', array_map(Closure::fromCallable([$driver, 'escapeIdentifier']), $value[1])) . ')';
                }
                else {
                    $clause = (new Where($value[1], 'ON'))->toSql($driver);
                }

                return trim("$type $table $as $clause");
            },
            $this->join,
        );

        $where = $this->whereData?->toSql($driver) ?? '';

        $groupData = implode(', ', array_map(static fn (string $value): string => Helpers::escapeTableName(Helpers::createTableName($value), $driver), $this->group));
        $group = $groupData !== '' ? "GROUP BY $groupData" : '';

        $having = $this->having !== [] ? (new Where($this->having, 'HAVING'))->toSql($driver) : '';

        $orderData = implode(', ', array_map(
            /**
             * @param Order $value
             */
            static fn (array $value): string => trim(Helpers::escapeTableName(Helpers::createTableName($value[0]), $driver) . ' ' . strtoupper($value[1] ?? '')),
            $this->order,
        ));
        $order = $orderData !== '' ? "ORDER BY $orderData" : '';

        $limit = $this->limit > 0 ? "LIMIT {$this->limit}" : '';
        $offset = $this->offset > 0 ? "OFFSET {$this->offset}" : '';

        return trim("$select $fields $from $join $where $group $having $order $limit $offset") . ';';
    }

    public function getParams(): array {
        return $this->whereData?->getParams() ?? [];
    }
}
