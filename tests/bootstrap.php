<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();

function test(callable $cb): void
{
	$cb();
}

function normalize(string $sql): string
{
    return trim(preg_replace('@\s+@', ' ', $sql) ?? $sql);
}

function prepareTempDir(): string
{
	$tempDir = __DIR__ . '/tmp/' . getmypid();
	@mkdir(dirname($tempDir), 0777, TRUE);
	Tester\Helpers::purge($tempDir);
	return $tempDir;
}
