<?php

	declare(strict_types=1);

	namespace CzProject\SqlGenerator;

    use DateTimeInterface;
    use Stringable;

	class Value
	{
		/** @var scalar|Stringable|DateTimeInterface */
		private int|float|bool|string|Stringable|DateTimeInterface $value;


		/**
		 * @param scalar|Stringable|DateTimeInterface $value
		 */
		public function __construct(int|float|bool|string|Stringable|DateTimeInterface $value)
		{
			$this->value = $value;
		}


        /**
         * @throws InvalidArgumentException
         */
		public function toString(IDriver $driver): string
        {
			return Helpers::formatValue($this->value, $driver);
		}


		/**
		 * @param scalar|Stringable|DateTimeInterface $value
		 */
		public static function create(int|float|bool|string|Stringable|DateTimeInterface $value): self
        {
			return new self($value);
		}
	}
