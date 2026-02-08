<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

class PartialFilter extends Filter
{
    public function __construct(
        protected bool $addRelationConstraint = true
    ) {}

    public function __invoke($query, $value, string $property)
    {
        if (str_contains($property, '.')) {
            return $this->applyRelationshipFilter($query, $value, $property);
        }

        return $query->whereRaw(
            "LOWER({$property}) LIKE ?",
            ['%'.strtolower($value).'%']
        );
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
            $q->whereRaw(
                "LOWER({$column}) LIKE ?",
                ['%'.strtolower($value).'%']
            );
        });
    }
}
