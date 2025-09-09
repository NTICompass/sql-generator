<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\OutOfRangeException;
	use CzProject\SqlGenerator\IDriver;
	use CzProject\SqlGenerator\IStatement;


	class IndexColumnDefinition implements IStatement
	{
		const ASC = 'ASC';
		const DESC = 'DESC';

		private string $name;

		private string $order;

		private ?int $length;


        /**
         * @throws OutOfRangeException
         */
        public function __construct(string $name, string $order = self::ASC, ?int $length = NULL)
		{
			$this->name = $name;
			$this->setOrder($order);
			$this->length = $length;
		}

        /**
         * @throws OutOfRangeException
         */
		private function setOrder(string $order): void
        {
			if ($order !== self::ASC && $order !== self::DESC) {
				throw new OutOfRangeException("Order type '$order' not found.");
			}

			$this->order = $order;
        }

		public function toSql(IDriver $driver): string
        {
			$output = $driver->escapeIdentifier($this->name);

			if ($this->length !== NULL) {
				$output .= ' (' . $this->length . ')';
			}

			if ($this->order !== self::ASC) {
				$output .= ' ' . $this->order;
			}

			return $output;
		}
	}
