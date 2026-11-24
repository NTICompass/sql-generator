<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\SqlDocument;
use CzProject\SqlGenerator\Statements\IndexDefinition;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(static function () {
	$sql = new SqlDocument;
	$driver = new Drivers\MysqlDriver;

	$contactTable = $sql->createTable('contact')
		->setComment('Clients table.');

	$contactTable->addColumn('id', 'INT', NULL, ['UNSIGNED' => NULL])
		->setAutoIncrement();

	$sql->alterTable('book')
		->dropColumn('name');

	$expected = [
        <<<'SQL'
            CREATE TABLE `contact` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT
            )
            COMMENT 'Clients table.';
        SQL,
        'ALTER TABLE `book` DROP COLUMN `name`;'
    ];

	Assert::same(
        array_map(Closure::fromCallable('normalize'), $expected),
        array_map(Closure::fromCallable('normalize'), $sql->getSqlQueries($driver)),
    );
});
