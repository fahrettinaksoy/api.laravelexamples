<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

abstract class Filter
{
    abstract public function __invoke($query, $value, string $property);
}
