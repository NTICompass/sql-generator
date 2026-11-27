<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Drivers;

use CzProject\SqlGenerator\Driver;
use DateTimeInterface;
use SQLite3;

/**
 * @see https://sqlite.org/lang_keywords.html
 */
class SqliteDriver implements Driver
{
    use DateParserTrait;

    public bool $modifyColumn = false;

    /**
     * @var string[]
     */
    private const TRANSACTION = [
        'start' => 'BEGIN TRANSACTION;',
        'commit' => 'COMMIT TRANSACTION;',
        'rollback' => 'ROLLBACK TRANSACTION;',
    ];

    private const LAST_ID = 'LAST_INSERT_ROWID()';

    public function escapeIdentifier(string $value): string
    {
        return '"'.str_replace('"', '""', $value).'"';
    }

    public function escapeText(string $value): string
    {
        return "'".SQLite3::escapeString($value)."'";
    }

    public function escapeBool(bool $value): string
    {
        return strval($value ? 1 : 0);
    }

    public function escapeDate(DateTimeInterface|string $value): string
    {
        return $this->escapeText($this->dateFormat($value, 'Y-m-d'));
    }

    public function escapeDateTime(DateTimeInterface|string $value): string
    {
        return $this->escapeText($this->dateFormat($value, 'Y-m-d H:i:s'));
    }

    public function transaction(string $action): string
    {
        return self::TRANSACTION[$action];
    }

    public function lastId(): string
    {
        return self::LAST_ID;
    }

    public function supportsInsertSet(): bool
    {
        return false;
    }

    public function supportsTableRename(): bool
    {
        return true;
    }

    public function supportsColumnRename(): bool
    {
        return true;
    }

    public function supportsColumnModify(): bool
    {
        return false;
    }
}
