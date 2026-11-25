<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\SqlDocument;
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
    $statement = new SqlDocument;

    $statement->select('information', ['rows' => 'COUNT(*)']);

    Assert::same('SELECT COUNT(*) AS `rows` FROM `information`;', normalize($statement->toSql($driver)));
});

test(static function () {
    $driver = new Drivers\MysqlDriver;
    $statement = new SqlDocument;

    $statement
        ->select('table', ['`name`', 'rows' => 'COUNT(*)'])
        ->addJoin('table2', ['name'])
        ->addGroup('name')
        ->addOrder('date');

    Assert::same(normalize(<<<'SQL'
        SELECT `name`, COUNT(*) AS `rows`
        FROM `table`
        JOIN `table2` USING(`name`)
        GROUP BY `name`
        ORDER BY `date`;
    SQL), normalize($statement->toSql($driver)));
});

test(static function () {
    $driver = new Drivers\MysqlDriver;
    $statement = new SqlDocument;

    $statement
        ->select('table', ['`name`', '`other_name`'])
        ->addJoin('table2', ['value' => 'Testing'], 'left')
        ->addGroup('name')
        ->addOrder('date');

    Assert::same(normalize(<<<'SQL'
        SELECT `name`, `other_name`
        FROM `table`
        LEFT JOIN `table2` ON `value` = 'Testing'
        GROUP BY `name`
        ORDER BY `date`;
    SQL), normalize($statement->toSql($driver)));
});