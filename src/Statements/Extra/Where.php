<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\InvalidArgumentException;
use CzProject\SqlGenerator\Statement;

class Where implements Statement
{
    /**
     * @param array<array-key, mixed | self> $data
     */
    public function __construct(
        private array $data,
        private string $statement = 'WHERE',
        private string $separator = ' AND ',
    )
    {}

    /**
     * @param array<array-key, mixed | self> $data
     */
    public static function nestedWhere(array $data, string $separator = ' OR '): self {
        return new self($data, statement: '', separator: $separator);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function toSql(Driver $driver): string
    {
        return trim($this->statement . ' ' .  Helpers::key_value_to_string(
            static function (string | int $field, mixed $value) use($driver): string {
                if ($value instanceof self) {
                    return '(' . $value->toSql($driver) . ')';
                }
                else {
                    return sprintf('%s = %s', $driver->escapeIdentifier($field), Helpers::formatValue($value, $driver));
                }
            },
            $this->data,
            $this->separator,
        ));
    }
}
