<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

class BeginsWithFilter extends Filter
{
    public function __invoke($query, $value, string $property)
    {
        if (str_contains($property, '.')) {
            return $this->applyRelationshipFilter($query, $value, $property);
        }

        $wrapped = $query->getGrammar()->wrap($property);

        return $query->whereRaw(
            "LOWER({$wrapped}) LIKE ?",
            [strtolower($value) . '%'],
        );
    }

    protected function applyRelationshipFilter($query, $value, string $property)
    {
        $parts = explode('.', $property);
        $column = array_pop($parts);
        $relation = implode('.', $parts);

        return $query->whereHas($relation, function ($q) use ($column, $value) {
            $wrapped = $q->getGrammar()->wrap($column);

            $q->whereRaw(
                "LOWER({$wrapped}) LIKE ?",
                [strtolower($value) . '%'],
            );
        });
    }
}
