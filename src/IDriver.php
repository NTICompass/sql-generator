<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator;

	use DateTimeInterface;

    /**
     * A few additional properties can be set to control the driver's capabilities:
     *  - `public bool $renameTable`
     *  - `public bool $renameColumn`
     *  - `public bool $modifyColumn`
     */
    interface IDriver
	{
		public function escapeIdentifier(string $value): string;

		public function escapeText(string $value): string;

		public function escapeBool(bool $value): string;

		public function escapeDate(DateTimeInterface|string $value): string;

		public function escapeDateTime(DateTimeInterface|string $value): string;

        /**
         * @param 'start'|'commit'|'rollback' $action
         */
        public function transaction(string $action): string;

        public function lastId(): string;
	}
