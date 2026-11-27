<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\InvalidArgumentException;
use CzProject\SqlGenerator\PreparedStatement;

class Where implements PreparedStatement
{
    /**
     * @param array<array-key, mixed | self> $data
     */
    public function __construct(
        private array $data,
        private string $statement = 'WHERE',
        private string $separator = ' AND ',
        private bool $preparedStatements = false,
    )
    {}

    /**
     * @param array<array-key, mixed | self> $data
     */
    public static function nestedWhere(array $data, string $separator = ' OR ', bool $preparedStatements = false): self {
        return new self($data, '', $separator, $preparedStatements);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function toSql(Driver $driver): string
    {
        return trim($this->statement . ' ' .  Helpers::key_value_to_string(
            function (string | int $field, mixed $value) use($driver): string {
                if ($value instanceof self) {
                    return '(' . $value->toSql($driver) . ')';
                }
                else {
                    return sprintf(
                        '%s = %s',
                        $driver->escapeIdentifier($field),
                        $this->preparedStatements ? '?' : Helpers::formatValue($value, $driver),
                    );
                }
            },
            $this->data,
            $this->separator,
        ));
    }

    public function getParams(): array {
        $ret = [];

        foreach ($this->data as $param) {
            if ($param instanceof self) {
                array_push($ret, ...$param->getParams());
            }
            else {
                $ret[] = Helpers::formatValueForPrepared($param);
            }
        }

        return $ret;
    }
}
