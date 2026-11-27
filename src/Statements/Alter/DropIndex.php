<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\NotImplementedException;
use CzProject\SqlGenerator\Statement;

class DropIndex implements Statement
{
    public function __construct(private ?string $index)
    {
    }

    /**
     * @throws NotImplementedException
     */
    public function toSql(Driver $driver): string
    {
        if ($driver->supportsColumnModify()) {
            if ($this->index === null) { // PRIMARY KEY
                if ($driver instanceof Drivers\MysqlDriver) {
                    return 'DROP PRIMARY KEY';
                }
                throw new NotImplementedException('Drop of primary key is not implemented for driver ' . $driver::class . '.');
            }

            return 'DROP INDEX ' . $driver->escapeIdentifier($this->index);
        }

        throw new NotImplementedException('Drop key is not implemented for driver ' . $driver::class . '.');
    }
}
