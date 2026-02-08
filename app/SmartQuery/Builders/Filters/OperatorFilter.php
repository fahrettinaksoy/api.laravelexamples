<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

use App\SmartQuery\Enums\FilterOperator;

class OperatorFilter extends Filter
{
    public function __construct(
        protected FilterOperator $operator = FilterOperator::EQUAL
    ) {}

    public function __invoke($query, $value, string $property)
    {
        if ($this->operator === FilterOperator::DYNAMIC) {
            $parsed = FilterOperator::parseDynamic((string) $value);
            $operator = $parsed['operator'];
            $value = $parsed['value'];
        } else {
            $operator = $this->operator->value;
        }

        if (str_contains($property, '.')) {
            return $this->applyRelationshipFilter($query, $value, $property, $operator);
        }

        return $query->where($property, $operator, $value);
    }

    protected function applyRelationshipFilter($query, $value, string $property, string $operator)
    {
        $parts = explode('.', $property);
        $column = array_pop($parts);
        $relation = implode('.', $parts);

        return $query->whereHas($relation, function ($q) use ($column, $operator, $value) {
            $q->where($column, $operator, $value);
        });
    }
}
