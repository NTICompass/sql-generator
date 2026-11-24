<?php

    declare(strict_types=1);

    namespace CzProject\SqlGenerator\Statements;

    use CzProject\SqlGenerator\Driver;
    use CzProject\SqlGenerator\Statement;

    class Transaction implements Statement
    {
        /**
         * @var 'start'|'commit'|'rollback'
         */
        private string $action;

        /**
         * @param 'start'|'commit'|'rollback' $action
         */
        public function __construct(string $action)
        {
            $this->action = $action;
        }

        public function toSql(Driver $driver): string
        {
            return $driver->transaction($this->action);
        }
    }