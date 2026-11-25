<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\OutOfRangeException;
use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Statement;

class IndexColumnDefinition implements Statement
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * @throws OutOfRangeException
     */
    public function __construct(
        private string $name,
        private string $order = self::ASC,
        private ?int $length = NULL,
    )
    {
        $this->setOrder($order);
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

    public function toSql(Driver $driver): string
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
