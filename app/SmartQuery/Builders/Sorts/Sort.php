<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Sorts;

abstract class Sort
{
    abstract public function __invoke($query, bool $descending, string $property): void;
}
