<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Statement;

class SqlCommand implements Statement
{
    public function __construct(private string $command)
    {
    }

    public function toSql(Driver $driver): string
    {
        return rtrim($this->command, ';') . ';';
    }
}
