<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers\MysqlDriver;
use CzProject\SqlGenerator\Statements\Where;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(static function () {
    $where = new Where(['name' => 'Testing', 'key' => 123]);

    Assert::same("WHERE `name` = 'Testing' AND `key` = 123", normalize($where->toSql(new MysqlDriver())));
});

test(static function () {
    $where = new Where([
        'name' => 'Testing',
        Where::nestedWhere([
            'key' => 123,
            'other_key' => 456,
        ]),
    ]);

    Assert::same("WHERE `name` = 'Testing' AND (`key` = 123 OR `other_key` = 456)", normalize($where->toSql(new MysqlDriver())));
});

test(static function () {
    $where = new Where([
        'name' => 'Testing',
        Where::nestedWhere([
            'key' => 123,
            'other_key' => 456,
        ], preparedStatements: true),
    ], preparedStatements: true);

    Assert::same("WHERE `name` = ? AND (`key` = ? OR `other_key` = ?)", normalize($where->toSql(new MysqlDriver())));
    Assert::same(['Testing', 123, 456], $where->getParams());
});

test(static function () {
    $where = new Where(['name' => 'Testing', 'key' => 123], 'SET', ', ');

    Assert::same("SET `name` = 'Testing', `key` = 123", normalize($where->toSql(new MysqlDriver())));
});