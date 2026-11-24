<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\Statements\Select;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(static function () {
    $driver = new Drivers\MysqlDriver;

    $select = new Select;

    Assert::same(normalize(<<<'SQL'
        SELECT *;
    SQL), normalize($select->toSql($driver)));
});
