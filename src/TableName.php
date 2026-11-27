<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator;

class TableName
{
    /** @var list<string> */
    private array $parts;

    public function __construct(string ...$parts)
    {
        $this->parts = array_values($parts);
    }

    public function toString(Driver $driver): string
    {
        $res = [];

        foreach ($this->parts as $part) {
            $res[] = $driver->escapeIdentifier($part);
        }

        return implode('.', $res);
    }

    public static function create(string $name): self
    {
        $parts = explode('.', $name);

        return new self(...$parts);
    }
}
