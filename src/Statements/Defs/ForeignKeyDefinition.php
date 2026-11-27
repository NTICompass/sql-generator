<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\OutOfRangeException;
use CzProject\SqlGenerator\Statement;
use CzProject\SqlGenerator\TableName;

use function is_array;

class ForeignKeyDefinition implements Statement
{
    public const ACTION_RESTRICT = 'RESTRICT';
    public const ACTION_NO_ACTION = 'NO ACTION';
    public const ACTION_CASCADE = 'CASCADE';
    public const ACTION_SET_NULL = 'SET NULL';

    /** @var list<string> */
    private array $columns;

    /** @var list<string> */
    private array $targetColumns;

    private string $onUpdateAction = self::ACTION_RESTRICT;

    private string $onDeleteAction = self::ACTION_RESTRICT;

    /**
     * @param string|string[] $columns
     * @param string|string[] $targetColumns
     */
    public function __construct(
        private string $name,
        array|string $columns,
        private string|TableName $targetTable,
        array|string $targetColumns,
    ) {
        $this->targetTable = Helpers::createTableName($targetTable);

        foreach (is_array($columns) ? $columns : [$columns] as $column) {
            $this->columns[] = $column;
        }

        foreach (is_array($targetColumns) ? $targetColumns : [$targetColumns] as $targetColumn) {
            $this->targetColumns[] = $targetColumn;
        }
    }

    /**
     * @throws OutOfRangeException
     */
    public function setOnUpdateAction(string $onUpdateAction): static
    {
        if (!$this->validateAction($onUpdateAction)) {
            throw new OutOfRangeException("Action '$onUpdateAction' is invalid.");
        }

        $this->onUpdateAction = $onUpdateAction;

        return $this;
    }

    /**
     * @throws OutOfRangeException
     */
    public function setOnDeleteAction(string $onDeleteAction): static
    {
        if (!$this->validateAction($onDeleteAction)) {
            throw new OutOfRangeException("Action '$onDeleteAction' is invalid.");
        }

        $this->onDeleteAction = $onDeleteAction;

        return $this;
    }

    public function toSql(Driver $driver): string
    {
        $output = 'CONSTRAINT ' . $driver->escapeIdentifier($this->name);
        $output .= ' FOREIGN KEY (';
        $output .= implode(', ', array_map([$driver, 'escapeIdentifier'], $this->columns));
        $output .= ') REFERENCES ' . Helpers::escapeTableName($this->targetTable, $driver) . ' (';
        $output .= implode(', ', array_map([$driver, 'escapeIdentifier'], $this->targetColumns));
        $output .= ')';
        $output .= ' ON DELETE ' . $this->onDeleteAction;
        $output .= ' ON UPDATE ' . $this->onUpdateAction;

        return $output;
    }

    private function validateAction(string $action): bool
    {
        return $action === self::ACTION_RESTRICT
            || $action === self::ACTION_NO_ACTION
            || $action === self::ACTION_CASCADE
            || $action === self::ACTION_SET_NULL;
    }
}
