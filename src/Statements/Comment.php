<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\Driver;
	use CzProject\SqlGenerator\Statement;

	class Comment implements Statement
	{
		private string $comment;

		public function __construct(string $comment)
		{
			$this->comment = $comment;
		}

		public function toSql(Driver $driver): string
        {
			return '-- ' . str_replace("\n", "\n-- ", Helpers::normalizeNewLines(trim($this->comment)));
		}
	}
