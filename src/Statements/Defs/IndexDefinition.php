<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\OutOfRangeException;
	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;


	class IndexDefinition implements IStatement
	{
		const TYPE_INDEX = 'INDEX';
		const TYPE_PRIMARY = 'PRIMARY';
		const TYPE_UNIQUE = 'UNIQUE';
		const TYPE_FULLTEXT = 'FULLTEXT';

		private ?string $name;

		private string $type;

		/** @var IndexColumnDefinition[] */
		private array $columns = [];

        /**
         * @throws OutOfRangeException
         */
        public function __construct(?string $name, string $type)
		{
			$this->name = $name;
			$this->setType($type);
		}

        /**
         * @throws OutOfRangeException
         */
        public function addColumn(string $column, string $order = IndexColumnDefinition::ASC, ?int $length = NULL): static
        {
			$this->columns[] = new IndexColumnDefinition($column, $order, $length);
			return $this;
		}

        /**
         * @throws OutOfRangeException
         */
		private function setType(string $type): void
        {
			$exists = $type === self::TYPE_INDEX
				|| $type === self::TYPE_PRIMARY
				|| $type === self::TYPE_UNIQUE
				|| $type === self::TYPE_FULLTEXT;

			if (!$exists) {
				throw new OutOfRangeException("Index type '$type' not found.");
			}

			$this->type = $type;
		}

		public function toSql(IDriver $driver): string
        {
			$output = $this->type !== self::TYPE_INDEX ? ($this->type . ' ') : '';
			$output .= 'KEY';

			if ($this->name !== NULL) {
				$output .= ' ' . $driver->escapeIdentifier($this->name);
			}

			$output .= ' (';
			$isFirst = TRUE;

			foreach ($this->columns as $column) {
				if ($isFirst) {
					$isFirst = FALSE;

				} else {
					$output .= ', ';
				}

				$output .= $column->toSql($driver);
			}

			$output .= ')';
			return $output;
		}
	}
