<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

class ExactFilter extends Filter
{
    public function __construct(
        protected bool $addRelationConstraint = true,
    ) {}

    public function __invoke($query, $value, string $property)
    {
        if (str_contains($property, '.')) {
            return $this->applyRelationshipFilter($query, $value, $property);
        }

        if (is_array($value)) {
            return $query->whereIn($property, $value);
        }

        $value = $this->convertBoolean($value);

        return $query->where($property, '=', $value);
    }

    protected function applyRelationshipFilter($query, $value, string $property)
    {
        $parts = explode('.', $property);
        $column = array_pop($parts);
        $relation = implode('.', $parts);

        if (! $this->addRelationConstraint) {
            return $query;
        }

        return $query->whereHas($relation, function ($q) use ($column, $value) {
            if (is_array($value)) {
                $q->whereIn($column, $value);
            } else {
                $q->where($column, '=', $this->convertBoolean($value));
            }
        });
    }

    protected function convertBoolean($value)
    {
        if ($value === 'true' || $value === '1') {
            return true;
        }

        if ($value === 'false' || $value === '0') {
            return false;
        }

        return $value;
    }
}
