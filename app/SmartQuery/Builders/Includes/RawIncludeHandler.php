<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Includes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;

class RawIncludeHandler
{
    public function apply(QueryBuilder $query, string $include, $model): void
    {
        if (str_ends_with($include, 'Count')) {
            $relationName = preg_replace('/Count$/', '', $include);
            $this->applyCountInclude($query, $relationName, $model);

            return;
        }

        if (str_ends_with($include, 'Exists')) {
            $relationName = preg_replace('/Exists$/', '', $include);
            $this->applyExistsInclude($query, $relationName, $model);

            return;
        }

        $this->applyJoinInclude($query, $include, $model);
    }

    protected function applyJoinInclude(QueryBuilder $query, string $relationName, $model): void
    {
        if (! method_exists($model, $relationName)) {
            return;
        }

        $relation = $model->{$relationName}();

        if ($relation instanceof BelongsTo) {
            $this->applyBelongsToJoin($query, $relation, $relationName);
        } elseif ($relation instanceof HasOne) {
            $this->applyHasOneJoin($query, $relation, $relationName);
        }
    }

    protected function applyBelongsToJoin(QueryBuilder $query, BelongsTo $relation, string $relationName): void
    {
        $relatedTable = $relation->getRelated()->getTable();
        $foreignKey = $relation->getForeignKeyName();
        $ownerKey = $relation->getOwnerKeyName();
        $parentTable = $relation->getParent()->getTable();

        $query->leftJoin(
            $relatedTable,
            "{$parentTable}.{$foreignKey}",
            '=',
            "{$relatedTable}.{$ownerKey}",
        );
    }

    protected function applyHasOneJoin(QueryBuilder $query, HasOne $relation, string $relationName): void
    {
        $relatedTable = $relation->getRelated()->getTable();
        $foreignKey = $relation->getForeignKeyName();
        $localKey = $relation->getLocalKeyName();
        $parentTable = $relation->getParent()->getTable();

        $query->leftJoin(
            $relatedTable,
            "{$relatedTable}.{$foreignKey}",
            '=',
            "{$parentTable}.{$localKey}",
        );
    }

    protected function applyCountInclude(QueryBuilder $query, string $relationName, $model): void
    {
        if (! method_exists($model, $relationName)) {
            return;
        }

        $relation = $model->{$relationName}();

        if ($relation instanceof HasMany || $relation instanceof HasOne) {
            $relatedTable = $relation->getRelated()->getTable();
            $foreignKey = $relation->getForeignKeyName();
            $localKey = $relation->getLocalKeyName();
            $parentTable = $relation->getParent()->getTable();
            $columnName = $relationName . '_count';

            $query->selectRaw(
                "(SELECT COUNT(*) FROM {$relatedTable} WHERE {$relatedTable}.{$foreignKey} = {$parentTable}.{$localKey}) as {$columnName}",
            );
        }
    }

    protected function applyExistsInclude(QueryBuilder $query, string $relationName, $model): void
    {
        if (! method_exists($model, $relationName)) {
            return;
        }

        $relation = $model->{$relationName}();

        if ($relation instanceof HasMany || $relation instanceof HasOne) {
            $relatedTable = $relation->getRelated()->getTable();
            $foreignKey = $relation->getForeignKeyName();
            $localKey = $relation->getLocalKeyName();
            $parentTable = $relation->getParent()->getTable();
            $columnName = $relationName . '_exists';

            $query->selectRaw(
                "(SELECT EXISTS(SELECT 1 FROM {$relatedTable} WHERE {$relatedTable}.{$foreignKey} = {$parentTable}.{$localKey})) as {$columnName}",
            );
        }
    }
}
