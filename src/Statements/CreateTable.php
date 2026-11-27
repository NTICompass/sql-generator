<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\DuplicateException;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\InvalidArgumentException;
use CzProject\SqlGenerator\OutOfRangeException;
use CzProject\SqlGenerator\Statement;
use CzProject\SqlGenerator\TableName;
use CzProject\SqlGenerator\Value;

class CreateTable implements Statement
{
    /** @var array<string, ColumnDefinition>  [name => ColumnDefinition] */
    private array $columns = [];

    /** @var array<string, IndexDefinition>  [name => IndexDefinition] */
    private array $indexes = [];

    /** @var array<string, ForeignKeyDefinition>  [name => ForeignKeyDefinition] */
    private array $foreignKeys = [];

    private ?string $comment;

    /** @var array<string, string|Value>  [name => value] */
    private array $options = [];

    public function __construct(private string|TableName $tableName)
    {
        $this->tableName = Helpers::createTableName($tableName);
    }

    /**
     * @param array<int|float|string>|null     $parameters
     * @param array<string, string|Value|null> $options
     *
     * @throws DuplicateException
     */
    public function addColumn(string $name, string $type, ?array $parameters = null, array $options = []): ColumnDefinition
    {
        if (isset($this->columns[$name])) {
            throw new DuplicateException("Column '$name' already exists.");
        }

        return $this->columns[$name] = new ColumnDefinition($name, $type, $parameters, $options);
    }

    /**
     * @throws DuplicateException|OutOfRangeException
     */
    public function addIndex(?string $name, string $type): IndexDefinition
    {
        if (isset($this->indexes[$name])) {
            throw new DuplicateException("Index '$name' already exists.");
        }

        return $this->indexes[$name] = new IndexDefinition($name, $type);
    }

    /**
     * @param string|string[] $columns
     * @param string|string[] $targetColumns
     *
     * @throws DuplicateException
     */
    public function addForeignKey(string $name, array|string $columns, string|TableName $targetTable, array|string $targetColumns): ForeignKeyDefinition
    {
        if (isset($this->foreignKeys[$name])) {
            throw new DuplicateException("Foreign key '$name' already exists.");
        }

        return $this->foreignKeys[$name] = new ForeignKeyDefinition($name, $columns, $targetTable, $targetColumns);
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function setOption(string $name, string|Value $value): static
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function toSql(Driver $driver): string
    {
        $output = 'CREATE TABLE ' . Helpers::escapeTableName($this->tableName, $driver) . " (\n";

        // columns
        $isFirst = true;

        foreach ($this->columns as $column) {
            if ($isFirst) {
                $isFirst = false;
            } else {
                $output .= ",\n";
            }

            $output .= "\t" . $column->toSql($driver);
        }

        foreach ($this->indexes as $index) {
            if ($isFirst) {
                $isFirst = false;
            } else {
                $output .= ",\n";
            }

            $output .= "\t" . $index->toSql($driver);
        }

        foreach ($this->foreignKeys as $foreignKey) {
            if ($isFirst) {
                $isFirst = false;
            } else {
                $output .= ",\n";
            }

            $output .= "\t" . $foreignKey->toSql($driver);
        }

        $output .= "\n)";

        if (isset($this->comment)) {
            $output .= "\n";
            $output .= 'COMMENT ' . $driver->escapeText($this->comment);
        }

        foreach ($this->options as $optionName => $optionValue) {
            $output .= "\n";
            $output .= $optionName . '=' . ($optionValue instanceof Value ? $optionValue->toString($driver) : $optionValue);
        }

        $output .= ';';

        return $output;
    }
}
