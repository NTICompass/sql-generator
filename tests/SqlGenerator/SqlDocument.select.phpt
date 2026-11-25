<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\Statements\Select;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(static function () {
    $driver = new Drivers\MysqlDriver;

    $select = new Select(tables: ['information']);

    Assert::same('SELECT * FROM `information`;', normalize($select->toSql($driver)));
});

test(static function () {
    $driver = new Drivers\MysqlDriver;

    $select = new Select(['rows' => 'COUNT(*)'], ['information']);

    Assert::same('SELECT COUNT(*) AS `rows` FROM `information`;', normalize($select->toSql($driver)));
});

test(static function () {
    $driver = new Drivers\MysqlDriver;

    $select = new Select(
        ['`name`', 'rows' => 'COUNT(*)'],
        ['table'],
        [['table2', ['name']]],
        group: ['name'],
        order: [['date']],
    );

    Assert::same(normalize(<<<'SQL'
        SELECT `name`, COUNT(*) AS `rows`
        FROM `table`
        JOIN `table2` USING(`name`)
        GROUP BY `name`
        ORDER BY `date`;
    SQL), normalize($select->toSql($driver)));
});