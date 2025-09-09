<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Drivers;
	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\IDriver;
    use CzProject\SqlGenerator\InvalidArgumentException;
    use CzProject\SqlGenerator\IStatement;
	use CzProject\SqlGenerator\Value;

	class ColumnDefinition implements IStatement
	{
		private string $name;

		private string $type;

		/** @var array<int|float|string> */
		private array $parameters = [];

		/** @var array<string, string|Value|NULL>  [name => value] */
		private array $options = [];

		private bool $nullable = FALSE;

		private mixed $defaultValue;

		private bool $autoIncrement = FALSE;

		private ?string $comment;

		/**
		 * @param array<int|float|string>|NULL $parameters
		 * @param array<string, string|Value|NULL> $options  [name => value]
		 */
		public function __construct(string $name, string $type, ?array $parameters = NULL, array $options = [])
		{
			$this->name = $name;
			$this->type = $type;
			$this->parameters = ($parameters !== NULL) ? $parameters : [];
			$this->options = $options;
		}

		public function setNullable(bool $nullable = TRUE): static
        {
			$this->nullable = $nullable;
			return $this;
		}

		public function setDefaultValue(mixed $defaultValue): static
        {
			$this->defaultValue = $defaultValue;
			return $this;
		}

		public function setAutoIncrement(bool $autoIncrement = TRUE): static
        {
			$this->autoIncrement = $autoIncrement;
			return $this;
		}

		public function setComment(?string $comment): static
        {
			$this->comment = $comment;
			return $this;
		}

        /**
         * @throws InvalidArgumentException
         */
        public function toSql(IDriver $driver): string
		{
			$output = $driver->escapeIdentifier($this->name) . ' ' . $this->type;

			if (!empty($this->parameters)) {
				$parameters = $this->parameters;
				array_walk($parameters, function (&$value) use ($driver) {
					$value = Helpers::formatValue($value, $driver);
				});
				$output .= '(' . implode(', ', $parameters) . ')';
			}

			$options = $this->options;
			$specialOptions = [];

			if ($driver instanceof Drivers\MysqlDriver) {
				$specialOptions = [
					'CHARACTER SET',
					'COLLATE',
				];
			}

			foreach ($specialOptions as $option) {
				if (isset($options[$option])) {
					$output .= ' ' . self::formatOption($option, $options[$option], $driver);
					unset($options[$option]);
				}
			}

			foreach ($options as $option => $value) {
				$output .= ' ' . self::formatOption($option, $value, $driver);
			}

			$output .= ' ' . ($this->nullable ? 'NULL' : 'NOT NULL');

			if (isset($this->defaultValue)) {
				$output .= ' DEFAULT ' . Helpers::formatValue($this->defaultValue, $driver);
			}

			if ($this->autoIncrement) {
				$output .= ' AUTO_INCREMENT';
			}

			if (isset($this->comment)) {
				$output .= ' COMMENT ' . $driver->escapeText($this->comment);
			}

			return $output;
		}

        /**
         * @throws InvalidArgumentException
         */
        private static function formatOption(string $name, string|Value|null $value, IDriver $driver): string
        {
			if ($value instanceof Value) {
				$value = $value->toString($driver);
			}

			return $name . ($value !== NULL ? (' ' . $value) : '');
		}
	}
