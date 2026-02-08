<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Sorts;

class DefaultSort extends Sort
{
    public function __invoke($query, bool $descending, string $property): void
    {
        $direction = $descending ? 'desc' : 'asc';
        $query->orderBy($property, $direction);
    }
}
