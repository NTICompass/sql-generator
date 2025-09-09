<?php
    declare(strict_types=1);

    namespace CzProject\SqlGenerator\Drivers;

    use DateTimeImmutable;
    use DateTimeInterface;
    use Exception;

    trait DateParserTrait {
        private function dateFormat(DateTimeInterface|string $value, string $format): string
        {
            try {
                $date = $value instanceof DateTimeInterface ? $value : new DateTimeImmutable($value);
                return $date->format($format);
            } catch (Exception $e) {
                return '';
            }
        }
    }