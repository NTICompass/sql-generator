<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;

	class Comment implements IStatement
	{
		private string $comment;

		public function __construct(string $comment)
		{
			$this->comment = $comment;
		}

		public function toSql(IDriver $driver): string {
			return '-- ' . str_replace("\n", "\n-- ", Helpers::normalizeNewLines(trim($this->comment)));
		}
	}
