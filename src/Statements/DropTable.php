<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\Statement;
use CzProject\SqlGenerator\TableName;

class DropTable implements Statement
{
    public function __construct(private string|TableName $tableName)
    {
        $this->tableName = Helpers::createTableName($tableName);
    }

    public function toSql(Driver $driver): string
    {
        return 'DROP TABLE ' . Helpers::escapeTableName($this->tableName, $driver) . ';';
    }
}
