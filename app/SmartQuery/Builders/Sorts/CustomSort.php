<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Sorts;

class CustomSort extends Sort
{
    public function __construct(
        protected Sort $sort
    ) {}

    public function __invoke($query, bool $descending, string $property): void
    {
        ($this->sort)($query, $descending, $property);
    }
}
