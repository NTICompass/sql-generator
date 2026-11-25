<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator;

interface Statement
{
    public function toSql(Driver $driver): string;
}
