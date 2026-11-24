<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\NotImplementedException;
use CzProject\SqlGenerator\Statements;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../libs/DummyDriver.php';

test(static function () {
	$table = new Statements\AlterTable('book');
	$table->dropIndex('contact');
	$table->dropIndex(NULL);

	Assert::same(
        'ALTER TABLE `book` DROP INDEX `contact`, DROP PRIMARY KEY;',
        normalize($table->toSql(new Drivers\MysqlDriver)),
    );

	Assert::exception(
        static fn (): string => $table->toSql(new Tests\DummyDriver),
        NotImplementedException::class,
        'Drop key is not implemented for driver Tests\DummyDriver.',
    );
});
