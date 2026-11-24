<?php
declare(strict_types=1);

use CzProject\SqlGenerator\SqlDocument;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(static function () {
	$sql = new SqlDocument;
	Assert::true($sql->isEmpty());

	$sql->createTable('book');
	Assert::false($sql->isEmpty());
});
