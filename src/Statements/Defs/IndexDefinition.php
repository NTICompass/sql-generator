<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\OutOfRangeException;
use CzProject\SqlGenerator\Statement;

class IndexDefinition implements Statement
{
    public const TYPE_INDEX = 'INDEX';
    public const TYPE_PRIMARY = 'PRIMARY';
    public const TYPE_UNIQUE = 'UNIQUE';
    public const TYPE_FULLTEXT = 'FULLTEXT';

    /** @var list<IndexColumnDefinition> */
    private array $columns = [];

    /**
     * @throws OutOfRangeException
     */
    public function __construct(private ?string $name, private string $type)
    {
        $this->setType($type);
    }

    /**
     * @throws OutOfRangeException
     */
    public function addColumn(string $column, string $order = IndexColumnDefinition::ASC, ?int $length = null): static
    {
        $this->columns[] = new IndexColumnDefinition($column, $order, $length);

        return $this;
    }

    /**
     * @throws OutOfRangeException
     */
    private function setType(string $type): void
    {
        $exists = $type === self::TYPE_INDEX
            || $type === self::TYPE_PRIMARY
            || $type === self::TYPE_UNIQUE
            || $type === self::TYPE_FULLTEXT;

        if (!$exists) {
            throw new OutOfRangeException("Index type '$type' not found.");
        }

        $this->type = $type;
    }

    public function toSql(Driver $driver): string
    {
        $output = $this->type !== self::TYPE_INDEX ? ($this->type . ' ') : '';
        $output .= 'KEY';

        if ($this->name !== null) {
            $output .= ' ' . $driver->escapeIdentifier($this->name);
        }

        $output .= ' (';
        $isFirst = true;

        foreach ($this->columns as $column) {
            if ($isFirst) {
                $isFirst = false;
            } else {
                $output .= ', ';
            }

            $output .= $column->toSql($driver);
        }

        $output .= ')';

        return $output;
    }
}
