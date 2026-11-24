<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\NotImplementedException;
use CzProject\SqlGenerator\SqlDocument;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../libs/DummyDriver.php';

test(static function () {
	$sql = new SqlDocument;
	$sql->renameTable('contact', 'client');

	Assert::same("ALTER TABLE `contact` RENAME TO `client`;", normalize($sql->toSql(new Drivers\MysqlDriver)));

	Assert::exception(
        static fn () : string => $sql->toSql(new Tests\DummyDriver),
        NotImplementedException::class,
        'Table rename is not implemented for driver Tests\DummyDriver.',
    );
});
