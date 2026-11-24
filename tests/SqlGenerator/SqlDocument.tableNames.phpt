<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\SqlDocument;
use CzProject\SqlGenerator\TableName;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(static function () {
	$sql = new SqlDocument;
	$driver = new Drivers\MysqlDriver;

	$authors = $sql->createTable(TableName::create('aaa.author'));
	$authors->addColumn('id', 'INT');

	$books = $sql->createTable(TableName::create('bbb.book'));
	$books->addColumn('id', 'INT');
	$books->addColumn('author_id', 'INT');
	$books->addForeignKey('fk_author_id', 'id', TableName::create('aaa.author'), 'id');

	$sql->renameTable(TableName::create('aaa.test'), TableName::create('bbb.test'));

	$sql->alterTable(TableName::create('aaa.test'))
		->addForeignKey('fk_test', 'book_id', TableName::create('bbb.book'), 'id');

	$sql->insert(TableName::create('aaa.test'), [
		'id' => 1,
		'name' => 'test',
	]);

    $expected = [
        <<<'SQL'
            CREATE TABLE `aaa`.`author` (
               `id` INT NOT NULL
            );
        SQL,

        <<<'SQL'
            CREATE TABLE `bbb`.`book` (
                `id` INT NOT NULL,
                `author_id` INT NOT NULL,
                CONSTRAINT `fk_author_id` FOREIGN KEY (`id`) REFERENCES `aaa`.`author` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
            );
        SQL,

        'ALTER TABLE `aaa`.`test` RENAME TO `bbb`.`test`;',

        <<<'SQL'
            ALTER TABLE `aaa`.`test`
                ADD CONSTRAINT `fk_test` FOREIGN KEY (`book_id`) REFERENCES `bbb`.`book` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
        SQL,

        "INSERT INTO `aaa`.`test` (`id`, `name`) VALUES (1, 'test');",
    ];

    Assert::same(
        array_map(Closure::fromCallable('normalize'), $expected),
        array_map(Closure::fromCallable('normalize'), $sql->getSqlQueries($driver)),
    );
});

test(static function () {
	$sql = new SqlDocument;
	$driver = new Drivers\MysqlDriver;

	$authors = $sql->createTable('aaa.author');
	$authors->addColumn('id', 'INT');

	$books = $sql->createTable('bbb.book');
	$books->addColumn('id', 'INT');
	$books->addColumn('author_id', 'INT');
	$books->addForeignKey('fk_author_id', 'id', 'aaa.author', 'id');

	$sql->renameTable('aaa.test', 'bbb.test');

	$sql->alterTable('aaa.test')
		->addForeignKey('fk_test', 'book_id', 'bbb.book', 'id');

	$sql->insert('aaa.test', [
		'id' => 1,
		'name' => 'test',
	]);

    $expected = [
        <<<'SQL'
            CREATE TABLE `aaa`.`author` (
               `id` INT NOT NULL
            );
        SQL,

        <<<'SQL'
            CREATE TABLE `bbb`.`book` (
                `id` INT NOT NULL,
                `author_id` INT NOT NULL,
                CONSTRAINT `fk_author_id` FOREIGN KEY (`id`) REFERENCES `aaa`.`author` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
            );
        SQL,

        'ALTER TABLE `aaa`.`test` RENAME TO `bbb`.`test`;',

        <<<'SQL'
            ALTER TABLE `aaa`.`test`
                ADD CONSTRAINT `fk_test` FOREIGN KEY (`book_id`) REFERENCES `bbb`.`book` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
        SQL,

        "INSERT INTO `aaa`.`test` (`id`, `name`) VALUES (1, 'test');",
    ];

    Assert::same(
        array_map(Closure::fromCallable('normalize'), $expected),
        array_map(Closure::fromCallable('normalize'), $sql->getSqlQueries($driver)),
    );
});
