<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\NotImplementedException;
use CzProject\SqlGenerator\Statement;

class DropForeignKey implements Statement
{
    public function __construct(private string $foreignKey)
    {
    }

    /**
     * @throws NotImplementedException
     */
    public function toSql(Driver $driver): string
    {
        if ($driver->supportsColumnModify()) {
            return 'DROP FOREIGN KEY ' . $driver->escapeIdentifier($this->foreignKey);
        }

        throw new NotImplementedException('Drop key is not implemented for driver ' . $driver::class . '.');
    }
}
