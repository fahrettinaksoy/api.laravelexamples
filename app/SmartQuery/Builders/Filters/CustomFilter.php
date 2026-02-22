<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

class CustomFilter extends Filter
{
    public function __construct(
        protected Filter $filter,
    ) {}

    public function __invoke($query, $value, string $property)
    {
        return ($this->filter)($query, $value, $property);
    }
}
