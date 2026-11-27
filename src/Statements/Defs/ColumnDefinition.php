<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator\Statements;

use CzProject\SqlGenerator\Driver;
use CzProject\SqlGenerator\Drivers;
use CzProject\SqlGenerator\Helpers;
use CzProject\SqlGenerator\InvalidArgumentException;
use CzProject\SqlGenerator\Statement;
use CzProject\SqlGenerator\Value;

class ColumnDefinition implements Statement
{
    /** @var array<int|float|string> */
    private array $parameters = [];

    private bool $nullable = false;

    private mixed $defaultValue;

    private bool $autoIncrement = false;

    private ?string $comment;

    /**
     * @param array<int|float|string>|null     $parameters
     * @param array<string, string|Value|null> $options    [name => value]
     */
    public function __construct(
        private string $name,
        private string $type,
        ?array $parameters = null,
        private array $options = [],
    ) {
        $this->parameters = $parameters !== null ? $parameters : [];
    }

    public function setNullable(bool $nullable = true): static
    {
        $this->nullable = $nullable;

        return $this;
    }

    public function setDefaultValue(mixed $defaultValue): static
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function setAutoIncrement(bool $autoIncrement = true): static
    {
        $this->autoIncrement = $autoIncrement;

        return $this;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function toSql(Driver $driver): string
    {
        $output = $driver->escapeIdentifier($this->name) . ' ' . $this->type;

        if ($this->parameters !== []) {
            $parameters = $this->parameters;
            array_walk($parameters, static function (&$value) use ($driver) {
                $value = Helpers::formatValue($value, $driver);
            });
            $output .= '(' . implode(', ', $parameters) . ')';
        }

        $options = $this->options;
        $specialOptions = [];

        if ($driver instanceof Drivers\MysqlDriver) {
            $specialOptions = [
                'CHARACTER SET',
                'COLLATE',
            ];
        }

        foreach ($specialOptions as $option) {
            if (isset($options[$option])) {
                $output .= ' ' . self::formatOption($option, $options[$option], $driver);
                unset($options[$option]);
            }
        }

        foreach ($options as $option => $value) {
            $output .= ' ' . self::formatOption($option, $value, $driver);
        }

        $output .= ' ' . ($this->nullable ? 'NULL' : 'NOT NULL');

        if (isset($this->defaultValue)) {
            $output .= ' DEFAULT ' . Helpers::formatValue($this->defaultValue, $driver);
        }

        if ($this->autoIncrement) {
            $output .= ' AUTO_INCREMENT';
        }

        if (isset($this->comment)) {
            $output .= ' COMMENT ' . $driver->escapeText($this->comment);
        }

        return $output;
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function formatOption(string $name, string|Value|null $value, Driver $driver): string
    {
        if ($value instanceof Value) {
            $value = $value->toString($driver);
        }

        return $name . ($value !== null ? (' ' . $value) : '');
    }
}
