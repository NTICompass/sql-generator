<?php
declare(strict_types=1);

use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\DuplicateException;
use CzProject\SqlGenerator\SqlDocument;
use CzProject\SqlGenerator\Statements\ColumnDefinition;
use CzProject\SqlGenerator\Statements\ForeignKeyDefinition;
use CzProject\SqlGenerator\Statements\IndexDefinition;
use CzProject\SqlGenerator\Value;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

test(static function () {
	$sql = new SqlDocument;
	$driver = new Drivers\MysqlDriver;

	$contactTable = $sql->createTable('contact')
		->setComment('Clients table.')
		->setOption('AUTO_INCREMENT', Value::create(123))
		->setOption('CHECKSUM', Value::create(FALSE))
		->setOption('COMPRESSION', Value::create('NONE'))
		->setOption('ENGINE', 'InnoDB');
	$contactTable->addColumn('id', 'INT', NULL, ['UNSIGNED' => NULL])
		->setAutoIncrement();
	$contactTable->addColumn('name', 'VARCHAR(100)')
		->setComment('Client name');
	$contactTable->addColumn('surname', 'VARCHAR(100)');
	$contactTable->addColumn('active', 'TINYINT', [1], [
			'UNSIGNED' => NULL,
			'MYOPTION' => Value::create('abc'),
		])
		->setDefaultValue(TRUE);
	$contactTable->addColumn('status', 'ENUM', ['new', 'verified'])
		->setDefaultValue('new');
	$contactTable->addColumn('created', 'DATETIME');
	$contactTable->addColumn('removed', 'DATETIME')
		->setNullable();

	$contactTable->addIndex(NULL, IndexDefinition::TYPE_PRIMARY)
		->addColumn('id');

	$contactTable->addIndex('name_surname', IndexDefinition::TYPE_UNIQUE)
		->addColumn('name', 'ASC', 100)
		->addColumn('surname', 'DESC', 100);

	$contactTable->addForeignKey('fk_creator', 'creator_id', 'user', 'id');

	Assert::same(normalize(<<<'SQL'
        CREATE TABLE `contact` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL COMMENT 'Client name',
            `surname` VARCHAR(100) NOT NULL,
            `active` TINYINT(1) UNSIGNED MYOPTION 'abc' NOT NULL DEFAULT 1,
            `status` ENUM('new', 'verified') NOT NULL DEFAULT 'new',
            `created` DATETIME NOT NULL,
            `removed` DATETIME NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name_surname` (`name` (100), `surname` (100) DESC),
            CONSTRAINT `fk_creator` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
        )
        COMMENT 'Clients table.'
        AUTO_INCREMENT=123
        CHECKSUM=0
        COMPRESSION='NONE'
        ENGINE=InnoDB;
    SQL), normalize($sql->toSql($driver)));
});

test(static function () {
	$sql = new SqlDocument;

	$contactTable = $sql->createTable('contact');
	$contactTable->addColumn('name', 'VARCHAR(100)');

	Assert::exception(
        static fn (): ColumnDefinition => $contactTable->addColumn('name', 'VARCHAR(50)'),
        DuplicateException::class,
        "Column 'name' already exists.",
    );
});

test(static function () {
	$sql = new SqlDocument;

	$contactTable = $sql->createTable('contact');
	$contactTable->addIndex('name', 'INDEX');

	Assert::exception(
        static fn (): IndexDefinition => $contactTable->addIndex('name', 'INDEX'),
        DuplicateException::class,
        "Index 'name' already exists.",
    );
});

test(static function () {
	$sql = new SqlDocument;

	$contactTable = $sql->createTable('contact');
	$contactTable->addForeignKey('fk_person', 'person_id', 'person', 'id');

	Assert::exception(
        static fn (): ForeignKeyDefinition => $contactTable->addForeignKey('fk_person', 'person_id', 'person', 'id'),
        DuplicateException::class,
        "Foreign key 'fk_person' already exists.",
    );
});
