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
        public function escapeIdentifier($value): string
        {
            return '"'.str_replace('"', '""', $value).'"';
        }

        public function escapeText($value): string
        {
            return "'".SQLite3::escapeString($value)."'";
        }

        public function escapeBool($value): string
        {
            return strval($value ? 1 : 0);
        }

        /**
         * @param DateTimeInterface|string $value
         * @throws Exception
         */
        public function escapeDate($value): string
        {
            if (!($value instanceof DateTimeInterface)) {
                $value = new DateTimeImmutable($value);
            }

            return $this->escapeText($value->format('Y-m-d'));
        }

        /**
         * @param DateTimeInterface|string $value
         * @throws Exception
         */
        public function escapeDateTime($value): string
        {
            if (!($value instanceof DateTimeInterface)) {
                $value = new DateTimeImmutable($value);
            }

            return $this->escapeText($value->format('Y-m-d H:i:s'));
        }
	}
