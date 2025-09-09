<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator;

	use DateTimeInterface;

    interface IDriver
	{
		public function escapeIdentifier(string $value): string;

		public function escapeText(string $value): string;

		public function escapeBool(bool $value): string;

		public function escapeDate(DateTimeInterface|string $value): string;

		public function escapeDateTime(DateTimeInterface|string $value): string;
	}
