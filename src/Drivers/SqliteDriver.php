<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Drivers;

	use CzProject\SqlGenerator\IDriver;
    use DateTimeImmutable;
    use DateTimeInterface;
    use Exception;
    use SQLite3;

    /**
     * @see https://sqlite.org/lang_keywords.html
     */
	class SqliteDriver implements IDriver
	{
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
            if (!($value instanceof DateTimeInterface)) {
                try {
                    $value = new DateTimeImmutable($value);
                } catch (Exception $e) {
                    return '';
                }
            }

            return $this->escapeText($value->format('Y-m-d'));
        }

        public function escapeDateTime(DateTimeInterface|string $value): string
        {
            if (!($value instanceof DateTimeInterface)) {
                try {
                    $value = new DateTimeImmutable($value);
                } catch (Exception $e) {
                    return '';
                }
            }

            return $this->escapeText($value->format('Y-m-d H:i:s'));
        }
	}
