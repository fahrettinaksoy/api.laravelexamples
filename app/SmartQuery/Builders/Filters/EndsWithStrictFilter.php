<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

class EndsWithStrictFilter extends Filter
{
    public function __invoke($query, $value, string $property)
    {
        if (str_contains($property, '.')) {
            return $this->applyRelationshipFilter($query, $value, $property);
        }

        return $query->where($property, 'LIKE', '%' . $value);
    }

    protected function applyRelationshipFilter($query, $value, string $property)
    {
        $parts = explode('.', $property);
        $column = array_pop($parts);
        $relation = implode('.', $parts);

        return $query->whereHas($relation, function ($q) use ($column, $value) {
            $q->where($column, 'LIKE', '%' . $value);
        });
    }
}
