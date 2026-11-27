<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Statement;
use CzProject\SqlGenerator\NotImplementedException;
use CzProject\SqlGenerator\OutOfRangeException;

class AddIndex implements Statement
{
    private IndexDefinition $definition;

    /**
     * @throws OutOfRangeException
     */
    public function __construct(?string $name, string $type)
    {
        $this->definition = new IndexDefinition($name, $type);
    }

    public function addColumn(string $column, string $order = IndexColumnDefinition::ASC, ?int $length = NULL): static
    {
        $this->definition->addColumn($column, $order, $length);
        return $this;
    }

    /**
     * @throws NotImplementedException
     */
    public function toSql(Driver $driver): string
    {
        if ($driver->supportsColumnModify()) {
            return 'ADD ' . $this->definition->toSql($driver);
        }
        else {
            throw new NotImplementedException('Add key is not implemented for driver ' . get_class($driver) . '.');
        }
    }
}
