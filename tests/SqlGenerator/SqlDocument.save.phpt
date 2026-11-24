<?php

declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\IOException;
use CzProject\SqlGenerator\SqlDocument;
use CzProject\SqlGenerator\Statements\IndexDefinition;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

define('TEMP_DIR', prepareTempDir());

test(static function () {
	$sql = new SqlDocument;
	$driver = new Drivers\MysqlDriver;
	$file = TEMP_DIR . '/subdir/file.sql';

	$sql->command('SET NAMES "utf8mb4"');
	$sql->save($file, $driver);

	Assert::same('SET NAMES "utf8mb4";', normalize(file_get_contents($file) ?: ''));
});


test(static function () {
	$sql = new SqlDocument;
	$sql->command('SET NAMES "utf8mb4"');
	$file = TEMP_DIR . '/subdir';

	Assert::exception(
        static fn () => $sql->save($file, new Drivers\MysqlDriver),
        IOException::class,
        "Unable to write file '$file'.",
    );
});
