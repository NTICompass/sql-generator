<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\Statement;
use CzProject\SqlGenerator\TableName;

class RenameTo implements Statement
{
    public function __construct(private TableName|string $tableName)
    {
        $this->tableName = Helpers::createTableName($tableName);
    }

    public function toSql(Driver $driver): string
    {
        return 'RENAME TO ' . Helpers::escapeTableName($this->tableName, $driver);
    }
}
