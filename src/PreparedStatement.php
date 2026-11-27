<?php
declare(strict_types=1);

namespace CzProject\SqlGenerator;

interface PreparedStatement extends Statement
{
    /**
     * @return list<mixed>
     */
    public function getParams(): array;
}
