<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator;

    use CzProject\SqlGenerator\Statements\Transaction;

    class SqlDocument
	{
		/** @var IStatement[] */
		private array $statements = [];

		public function addStatement(IStatement $statement): static
        {
			$this->statements[] = $statement;
			return $this;
		}

		public function isEmpty(): bool
        {
			return empty($this->statements);
		}

		/**
		 * @return string[]
		 */
		public function getSqlQueries(IDriver $driver): array
        {
			$output = [];

			foreach ($this->statements as $statement) {
				$output[] = $statement->toSql($driver);
			}

			return $output;
		}

		public function toSql(IDriver $driver): string
        {
			$output = '';
			$first = TRUE;

			foreach ($this->statements as $statement) {
				if ($first) {
					$first = FALSE;

				} else {
					$output .= "\n";
				}

				$output .= $statement->toSql($driver);
				$output .= "\n";
			}

			return $output;
		}

		/**
		 * @throws IOException
		 */
		public function save(string $file, IDriver $driver): void
        {
			// create directory
			$dir = dirname($file);

			if (!is_dir($dir) && !@mkdir($dir, 0777, TRUE) && !is_dir($dir)) { // @ - dir may already exist
				throw new IOException("Unable to create directory '$dir'.");
			}

			// write file
			$content = $this->toSql($driver);

			if (@file_put_contents($file, $content) === FALSE) { // @ is escalated to exception
				throw new IOException("Unable to write file '$file'.");
			}
		}

        /**
         * @param 'start'|'commit'|'rollback' $action
         * @return Transaction
         */
        public function transaction(string $action): Statements\Transaction
        {
            $statement = new Statements\Transaction($action);
            $this->addStatement($statement);
            return $statement;
        }

        /**
         * @param array<string, mixed> $data
         */
		public function insert(string|TableName $tableName, array $data): Statements\Insert
        {
			$statement = new Statements\Insert($tableName, $data);
			$this->addStatement($statement);
			return $statement;
		}

        /**
         * @param array<string, mixed> $data
         * @param array<string, string> $where
         */
        public function update(string|TableName $tableName, array $data, array $where): Statements\Update
        {
            $statement = new Statements\Update($tableName, $data, $where);
            $this->addStatement($statement);
            return $statement;
        }

		public function createTable(string|TableName $tableName): Statements\CreateTable
        {
			$statement = new Statements\CreateTable($tableName);
			$this->addStatement($statement);
			return $statement;
		}

		public function dropTable(string|TableName $tableName): Statements\DropTable
        {
			$statement = new Statements\DropTable($tableName);
			$this->addStatement($statement);
			return $statement;
		}

		public function renameTable(string|TableName $oldTable, string|TableName $newTable): Statements\RenameTable
        {
			$statement = new Statements\RenameTable($oldTable, $newTable);
			$this->addStatement($statement);
			return $statement;
		}

		public function alterTable(string|TableName $tableName): Statements\AlterTable
        {
			$statement = new Statements\AlterTable($tableName);
			$this->addStatement($statement);
			return $statement;
		}

		public function command(string $command): Statements\SqlCommand
        {
			$statement = new Statements\SqlCommand($command);
			$this->addStatement($statement);
			return $statement;
		}

		public function comment(string $comment): Statements\Comment
        {
			$statement = new Statements\Comment($comment);
			$this->addStatement($statement);
			return $statement;
		}
	}
