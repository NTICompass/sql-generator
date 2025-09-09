<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator;

	interface IStatement
	{
		public function toSql(IDriver $driver): string;
	}
