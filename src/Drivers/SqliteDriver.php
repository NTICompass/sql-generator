<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Drivers;

	use CzProject\SqlGenerator\IDriver;
    use DateTimeInterface;
    use SQLite3;

    /**
     * @see https://sqlite.org/lang_keywords.html
     */
	class SqliteDriver implements IDriver
	{
        use DateParserTrait;

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
	}
