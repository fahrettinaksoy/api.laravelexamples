<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BelongsToFilter extends Filter
{
    public function __construct(
        protected ?string $relationshipPath = null
    ) {}

    public function __invoke($query, $value, string $property)
    {
        $relationshipPath = $this->relationshipPath ?? $property;

        if ($query instanceof EloquentBuilder) {
            return $this->applyEloquentFilter($query, $value, $relationshipPath);
        }

        return $this->applyRawFilter($query, $value, $relationshipPath);
    }

    protected function applyEloquentFilter(EloquentBuilder $query, $value, string $relationshipPath)
    {
        if (str_contains($relationshipPath, '.')) {
            return $query->whereHas($relationshipPath, function ($q) use ($value) {
                $q->where($q->getModel()->getKeyName(), $value);
            });
        }

        $relation = $query->getModel()->{$relationshipPath}();

        if (! $relation instanceof BelongsTo) {
            throw new \InvalidArgumentException(
                "Relationship '{$relationshipPath}' must be a BelongsTo relationship"
            );
        }

        return $query->where($relation->getForeignKeyName(), $value);
    }

    protected function applyRawFilter($query, $value, string $relationshipPath)
    {
        $foreignKey = $relationshipPath.'_id';

        return $query->where($foreignKey, $value);
    }
}
