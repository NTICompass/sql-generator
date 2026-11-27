<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\SqlDocument;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(static function () {
    $sql = new SqlDocument;
    $driver = new Drivers\MysqlDriver;

    $sql->update('contact', [
        'active' => FALSE,
        'created' => new DateTime('2016-12-31 16:12:31', new DateTimeZone('UTC')),
        'removed' => NULL,
    ], [
        'name' => 'Harry',
        'surname' => 'Potter',
    ]);

    Assert::same(normalize(<<<'SQL'
        UPDATE `contact`
        SET `active` = 0,
            `created` = '2016-12-31 16:12:31',
            `removed` = NULL
        WHERE `name` = 'Harry'
          AND `surname` = 'Potter';
    SQL), normalize($sql->toSql($driver)));
});

test(static function () {
    $sql = new SqlDocument;
    $driver = new Drivers\MysqlDriver;

    $date = new DateTime('2016-12-31 16:12:31', new DateTimeZone('UTC'));

    $sql->update('contact', [
        'active' => FALSE,
        'created' => $date,
        'removed' => NULL,
    ], [
        'name' => 'Harry',
        'surname' => 'Potter',
    ], preparedStatements: true);

    Assert::same(normalize(<<<'SQL'
        UPDATE `contact`
        SET `active` = ?,
            `created` = ?,
            `removed` = ?
        WHERE `name` = ?
          AND `surname` = ?;
    SQL), normalize($sql->toSql($driver)));

    Assert::same([
        false,
        $date,
        null,
        'Harry',
        'Potter',
    ], $sql->getParams());
});