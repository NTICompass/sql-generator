<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Statement;

class RenameColumnTo implements Statement
{
    public function __construct(private string $oldName, private string $newName)
    {
    }

    public function toSql(Driver $driver): string
    {
        $oldName = $driver->escapeIdentifier($this->oldName);
        $newName = $driver->escapeIdentifier($this->newName);

        return "RENAME COLUMN $oldName TO $newName";
    }
}
