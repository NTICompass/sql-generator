<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\SqlDocument;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(static function () {
	$sql = new SqlDocument;
	$driver = new Drivers\MysqlDriver;

	$sql->insert('contact', [
		'name' => 'Harry',
		'surname' => 'Potter',
		'active' => FALSE,
		'created' => new DateTime('2016-12-31 16:12:31', new DateTimeZone('UTC')),
		'removed' => NULL,
	]);

	Assert::same(normalize(<<<'SQL'
        INSERT INTO `contact` (`name`, `surname`, `active`, `created`, `removed`)
        VALUES ('Harry', 'Potter', 0, '2016-12-31 16:12:31', NULL);
    SQL), normalize($sql->toSql($driver)));
});

test(static function () {
    $sql = new SqlDocument;
    $driver = new Drivers\MysqlDriver;

    $sql->insert('contact', [
        'name' => 'Harry',
        'surname' => 'Potter',
        'active' => FALSE,
        'created' => new DateTime('2016-12-31 16:12:31', new DateTimeZone('UTC')),
        'removed' => NULL,
    ], useSet: true);

    Assert::same(normalize(<<<'SQL'
        INSERT INTO `contact`
        SET `name` = 'Harry',
            `surname` = 'Potter',
            `active` = 0,
            `created` = '2016-12-31 16:12:31',
            `removed` = NULL;
    SQL), normalize($sql->toSql($driver)));
});

test(static function () {
    $sql = new SqlDocument;
    $driver = new Drivers\MysqlDriver;

    $sql->insert('contact', [
        'name' => 'Harry',
        'surname' => 'Potter',
        'active' => FALSE,
        'created' => new DateTime('2016-12-31 16:12:31', new DateTimeZone('UTC')),
        'removed' => NULL,
    ], preparedStatements: true);

    Assert::same(normalize(<<<'SQL'
        INSERT INTO `contact` (`name`, `surname`, `active`, `created`, `removed`)
        VALUES (?, ?, ?, ?, ?);
    SQL), normalize($sql->toSql($driver)));

    Assert::same(['Harry', 'Potter', 0, '2016-12-31 16:12:31', null], $sql->getParams());
});

test(static function () {
    $sql = new SqlDocument;
    $driver = new Drivers\MysqlDriver;

    $sql->insert('contact', [
        'name' => 'Harry',
        'surname' => 'Potter',
        'active' => FALSE,
        'created' => new DateTime('2016-12-31 16:12:31', new DateTimeZone('UTC')),
        'removed' => NULL,
    ], preparedStatements: true, useSet: true);

    Assert::same(normalize(<<<'SQL'
        INSERT INTO `contact`
        SET `name` = ?,
            `surname` = ?,
            `active` = ?,
            `created` = ?,
            `removed` = ?;
    SQL), normalize($sql->toSql($driver)));

    Assert::same(['Harry', 'Potter', 0, '2016-12-31 16:12:31', null], $sql->getParams());
});
