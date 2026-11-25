<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Statement;

class Transaction implements Statement
{
    /**
     * @param 'start'|'commit'|'rollback' $action
     */
    public function __construct(private string $action)
    {}

    public function toSql(Driver $driver): string
    {
        return $driver->transaction($this->action);
    }
}