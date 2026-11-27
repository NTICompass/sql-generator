<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator;

/**
 * When implementing this interface, you should also add `bool $preparedStatements` to your constructor.
 */
interface PreparedStatement extends Statement
{
    /**
     * @return list<mixed>
     */
    public function getParams(): array;
}
