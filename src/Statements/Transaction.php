<?php

    declare(strict_types=1);

    namespace CzProject\SqlGenerator\Statements;

    use CzProject\SqlGenerator\IDriver;
    use CzProject\SqlGenerator\IStatement;

    class Transaction implements IStatement {
        /**
         * @var 'start'|'commit'|'rollback'
         */
        private string $action;

        /**
         * @param 'start'|'commit'|'rollback' $action
         */
        public function __construct(string $action) {
            $this->action = $action;
        }

        public function toSql(IDriver $driver): string {
            return $driver->transaction($this->action);
        }
    }