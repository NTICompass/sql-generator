<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\IDriver;
    use CzProject\SqlGenerator\InvalidArgumentException;
    use CzProject\SqlGenerator\IStatement;
    use CzProject\SqlGenerator\NotImplementedException;
    use CzProject\SqlGenerator\Value;

	class ModifyColumn implements IStatement
	{
		const POSITION_FIRST = TRUE;
		const POSITION_LAST = FALSE;

		private ColumnDefinition $definition;

		private string|bool $position = self::POSITION_LAST;

		/**
		 * @param array<int|float|string> $parameters
		 * @param array<string, string|Value|NULL> $options  [name => value]
		 */
		public function __construct(string $name, string $type, ?array $parameters = NULL, array $options = [])
		{
			$this->definition = new ColumnDefinition($name, $type, $parameters, $options);
		}

		public function moveToFirstPosition(): static
        {
			$this->position = self::POSITION_FIRST;
			return $this;
		}

		public function moveAfterColumn(string $column): static
        {
			$this->position = $column;
			return $this;
		}

		public function moveToLastPosition(): static
        {
			$this->position = self::POSITION_LAST;
			return $this;
		}

		public function setNullable(bool $nullable = TRUE): static
        {
			$this->definition->setNullable($nullable);
			return $this;
		}

		public function setDefaultValue(mixed $defaultValue): static
        {
			$this->definition->setDefaultValue($defaultValue);
			return $this;
		}

		public function setAutoIncrement(bool $autoIncrement = TRUE): static
        {
			$this->definition->setAutoIncrement($autoIncrement);
			return $this;
		}

		public function setComment(?string $comment): static
        {
			$this->definition->setComment($comment);
			return $this;
		}

        /**
         * @throws NotImplementedException|InvalidArgumentException
         */
        public function toSql(IDriver $driver): string
		{
            if ($driver->modifyColumn ?? true) {
                $output = 'MODIFY COLUMN ' . $this->definition->toSql($driver);

                if ($this->position === self::POSITION_FIRST) {
                    $output .= ' FIRST';

                } elseif ($this->position !== self::POSITION_LAST) {
                    $output .= ' AFTER ' . $driver->escapeIdentifier($this->position);
                }

                return $output . ';';
            }
            else {
                throw new NotImplementedException('Modify column is not implemented for driver ' . get_class($driver) . '.');
            }
		}
	}
