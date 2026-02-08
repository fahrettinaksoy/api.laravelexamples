<?php

declare(strict_types=1);

namespace App\SmartQuery\Exceptions;

use Exception;

class InvalidFilterQuery extends Exception
{
    public static function filterNotAllowed(string $filter, array $allowedFilters): self
    {
        $allowedFiltersString = implode(', ', $allowedFilters);

        return new static(
            "Filter '{$filter}' is not allowed. Allowed filters: {$allowedFiltersString}"
        );
    }

    public static function filtersNotAllowed(array $filters, array $allowedFilters): self
    {
        $filtersString = implode(', ', $filters);
        $allowedFiltersString = implode(', ', $allowedFilters);

        return new static(
            "Filters '{$filtersString}' are not allowed. Allowed filters: {$allowedFiltersString}"
        );
    }
}
