<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\SqlDocument;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(static function () {
	$sql = new SqlDocument;
	$driver = new Drivers\MysqlDriver;

	$sql->delete('contact', [
		'name' => 'Harry',
		'surname' => 'Potter',
	]);

	Assert::same(normalize(<<<'SQL'
        DELETE FROM `contact` WHERE `name` = 'Harry' AND `surname` = 'Potter';
    SQL), normalize($sql->toSql($driver)));
});

test(static function () {
    $sql = new SqlDocument;
    $driver = new Drivers\MysqlDriver;

    $sql->delete('contact', [
        'name' => 'Harry',
        'surname' => 'Potter',
    ], preparedStatements: true);

    Assert::same('DELETE FROM `contact` WHERE `name` = ? AND `surname` = ?;', normalize($sql->toSql($driver)));
    Assert::same(['Harry', 'Potter'], $sql->getParams());
});