<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator\Statements;

	use CzProject\SqlGenerator\Helpers;
	use CzProject\SqlGenerator\IDriver;
    use CzProject\SqlGenerator\InvalidArgumentException;
    use CzProject\SqlGenerator\IStatement;
	use CzProject\SqlGenerator\TableName;
	use CzProject\SqlGenerator\Value;

	class AlterTable implements IStatement
	{
		private TableName|string $tableName;

		/** @var IStatement[] */
		private array $statements = [];

		private ?string $comment;

		/** @var array<string, string|Value> [name => value] */
		private array $options = [];

		public function __construct(string|TableName $tableName)
		{
			$this->tableName = Helpers::createTableName($tableName);
		}

        public function rename(string|TableName $newName): RenameTo {
            return $this->statements[] = new RenameTo($newName);
        }

        public function renameColumn(string $oldName, string $newName): RenameColumnTo
        {
            return $this->statements[] = new RenameColumnTo($oldName, $newName);
        }

		/**
		 * @param array<int|float|string> $parameters
		 * @param array<string, string|Value|NULL> $options  [name => value]
		 */
		public function addColumn(string $name, string $type, ?array $parameters = NULL, array $options = []): AddColumn {
			return $this->statements[] = new AddColumn($name, $type, $parameters, $options);
		}

		public function dropColumn(string $column): DropColumn {
			return $this->statements[] = new DropColumn($column);
		}

		/**
		 * @param array<int|float|string> $parameters
		 * @param array<string, string|Value|NULL> $options  [name => value]
		 */
		public function modifyColumn(string $name, string $type, ?array $parameters = NULL, array $options = []): ModifyColumn {
			return $this->statements[] = new ModifyColumn($name, $type, $parameters, $options);
		}

		public function addIndex(?string $name, string $type): AddIndex {
			return $this->statements[] = new AddIndex($name, $type);
		}

		public function dropIndex(?string $index): DropIndex {
			return $this->statements[] = new DropIndex($index);
		}

		/**
		 * @param string|string[] $columns
		 * @param string|string[] $targetColumns
		 */
		public function addForeignKey(string $name, array|string $columns, string|TableName $targetTable, array|string $targetColumns): AddForeignKey {
			return $this->statements[] = new AddForeignKey($name, $columns, $targetTable, $targetColumns);
		}

		public function dropForeignKey(string $foreignKey): DropForeignKey {
			return $this->statements[] = new DropForeignKey($foreignKey);
		}

		public function setComment(?string $comment): static {
			$this->comment = $comment;
			return $this;
		}

		public function setOption(string $name, string|Value $value): static {
			$this->options[$name] = $value;
			return $this;
		}

        /**
         * @throws InvalidArgumentException
         */
        public function toSql(IDriver $driver): string {
			if (empty($this->statements) && empty($this->options) && !isset($this->comment)) {
				return '';
			}

			$output = 'ALTER TABLE ' . Helpers::escapeTableName($this->tableName, $driver) . "\n";
			$isFirst = TRUE;

			foreach ($this->statements as $statement) {
				if ($isFirst) {
					$isFirst = FALSE;

				} else {
					$output .= ",\n";
				}

				$output .= $statement->toSql($driver);
			}

			if (isset($this->comment)) {
				if ($isFirst) {
					$isFirst = FALSE;

				} else {
					$output .= ",\n";
				}

				$output .= 'COMMENT ' . $driver->escapeText($this->comment);
			}

			foreach ($this->options as $optionName => $optionValue) {
				if ($isFirst) {
					$isFirst = FALSE;

				} else {
					$output .= ",\n";
				}

				$output .= $optionName . '=' . ($optionValue instanceof Value ? $optionValue->toString($driver) : $optionValue);
			}

			$output .= ';';
			return $output;
		}
	}
