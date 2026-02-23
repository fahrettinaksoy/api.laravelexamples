<?php

declare(strict_types=1);

namespace App\SmartQuery\Exceptions;

use Exception;

class InvalidFilterQuery extends Exception
{
    public static function filterNotAllowed(string $filter, array $allowedFilters): self
    {
        return new static(
            __('api.smartquery.filter_not_allowed', [
                'filter' => $filter,
                'allowed' => implode(', ', $allowedFilters),
            ]),
        );
    }

    public static function filtersNotAllowed(array $filters, array $allowedFilters): self
    {
        return new static(
            __('api.smartquery.filters_not_allowed', [
                'filters' => implode(', ', $filters),
                'allowed' => implode(', ', $allowedFilters),
            ]),
        );
    }

    public static function invalidDynamicOperator(string $value): self
    {
        return new static(
            __('api.smartquery.invalid_dynamic_operator', [
                'value' => $value,
            ]),
        );
    }
}
