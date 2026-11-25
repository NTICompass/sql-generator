<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Statement;

class DropColumn implements Statement
{
    public function __construct(private string $column)
    {}

    public function toSql(Driver $driver): string
    {
        return 'DROP COLUMN ' . $driver->escapeIdentifier($this->column);
    }
}
