<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator;

use Generator;

class SqlDocument
{
    /** @var Statement[] */
    private array $statements = [];

    public function addStatement(Statement $statement): static
    {
        $this->statements[] = $statement;
        return $this;
    }

    public function isEmpty(): bool
    {
        return $this->statements === [];
    }

    /**
     * @return Generator<int, string>
     */
    private function getQueries(Driver $driver): Generator {
        foreach ($this->statements as $statement) {
           yield $statement->toSql($driver);
        }
    }

    /**
     * @return string[]
     */
    public function getSqlQueries(Driver $driver): array
    {
        return iterator_to_array($this->getQueries($driver));
    }

    public function toSql(Driver $driver): string
    {
        $statements = $this->getSqlQueries($driver);
        return $statements === [] ? '' : implode("\n\n", $statements) . "\n";
    }

    /**
     * @return list<mixed>
     */
    public function getParams(): array {
        $ret = [];

        foreach ($this->statements as $statement) {
            if ($statement instanceof PreparedStatement) {
                array_push($ret, ...$statement->getParams());
            }
        }

        return $ret;
    }

    /**
     * @throws IOException
     */
    public function save(string $file, Driver $driver): void
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
     * @return Statements\Transaction
     */
    public function transaction(string $action): Statements\Transaction
    {
        $statement = new Statements\Transaction($action);
        $this->addStatement($statement);
        return $statement;
    }

    /**
     * @param array<array-key, string | Statements\Select> $fields $fields
     * @param array<array-key, mixed | Statements\Where> $where
     */
    public function select(string|TableName $tableName, array $fields = [], array $where = [], bool $preparedStatements = false): Statements\Select {
        $statement = new Statements\Select($fields, [$tableName], where: $where, preparedStatements: $preparedStatements);
        $this->addStatement($statement);
        return $statement;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function insert(string|TableName $tableName, array $data, bool $preparedStatements = false, bool $useSet = false): Statements\Insert
    {
        $statement = new Statements\Insert($tableName, $data, preparedStatements: $preparedStatements, useSet: $useSet);
        $this->addStatement($statement);
        return $statement;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $where
     */
    public function update(string|TableName $tableName, array $data, array $where, bool $preparedStatements = false): Statements\Update
    {
        $statement = new Statements\Update($tableName, $data, $where, preparedStatements: $preparedStatements);
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
