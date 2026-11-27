<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\InvalidArgumentException;
use CzProject\SqlGenerator\NotImplementedException;
use CzProject\SqlGenerator\TableName;

class RenameColumn extends AlterTable
{
    public function __construct(
        TableName|string $tableName,
        private string $oldColumn,
        private string $newColumn,
    )
    {
        parent::__construct($tableName);
    }

    /**
     * @throws NotImplementedException|InvalidArgumentException
     */
    public function toSql(Driver $driver): string
    {
        if ($driver->supportsColumnRename()) {
            $this->renameColumn($this->oldColumn, $this->newColumn);

            return parent::toSql($driver);
        }
        else {
            throw new NotImplementedException('Column rename is not implemented for driver ' . get_class($driver) . '.');
        }
    }
}
