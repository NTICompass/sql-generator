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

	$sql->command('SET NAMES "utf8mb4"');

	Assert::same('SET NAMES "utf8mb4";', normalize($sql->toSql($driver)));
});
