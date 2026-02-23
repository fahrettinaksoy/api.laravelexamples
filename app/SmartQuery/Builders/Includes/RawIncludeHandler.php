<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Includes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Log;

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

        match (true) {
            $relation instanceof BelongsTo => $this->applyBelongsToJoin($query, $relation),
            $relation instanceof HasOne => $this->applyHasOneJoin($query, $relation),
            $relation instanceof HasMany => $this->applyHasManyJoin($query, $relation),
            $relation instanceof BelongsToMany => $this->applyBelongsToManyJoin($query, $relation),
            default => Log::warning('SmartQuery: Unsupported relation type for raw join include', [
                'relation' => $relationName,
                'type' => get_class($relation),
                'model' => get_class($model),
            ]),
        };
    }

    protected function applyBelongsToJoin(QueryBuilder $query, BelongsTo $relation): void
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

    protected function applyHasOneJoin(QueryBuilder $query, HasOne $relation): void
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

    protected function applyHasManyJoin(QueryBuilder $query, HasMany $relation): void
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

    protected function applyBelongsToManyJoin(QueryBuilder $query, BelongsToMany $relation): void
    {
        $pivotTable = $relation->getTable();
        $foreignPivotKey = $relation->getForeignPivotKeyName();
        $relatedPivotKey = $relation->getRelatedPivotKeyName();
        $parentKey = $relation->getParentKeyName();
        $relatedKey = $relation->getRelatedKeyName();
        $parentTable = $relation->getParent()->getTable();
        $relatedTable = $relation->getRelated()->getTable();

        $query->leftJoin(
            $pivotTable,
            "{$parentTable}.{$parentKey}",
            '=',
            "{$pivotTable}.{$foreignPivotKey}",
        );

        $query->leftJoin(
            $relatedTable,
            "{$pivotTable}.{$relatedPivotKey}",
            '=',
            "{$relatedTable}.{$relatedKey}",
        );
    }

    protected function applyCountInclude(QueryBuilder $query, string $relationName, $model): void
    {
        if (! method_exists($model, $relationName)) {
            return;
        }

        $relation = $model->{$relationName}();
        $grammar = $query->getGrammar();
        $parentTable = $relation->getParent()->getTable();
        $columnName = $relationName . '_count';
        $subAlias = $relationName . '_cnt';

        if ($relation instanceof HasMany || $relation instanceof HasOne) {
            $relatedTable = $relation->getRelated()->getTable();
            $foreignKey = $relation->getForeignKeyName();
            $localKey = $relation->getLocalKeyName();

            $subQuery = $query->connection->table($relatedTable)
                ->select($foreignKey)
                ->selectRaw(sprintf('COUNT(*) as %s', $grammar->wrap('aggregate_count')))
                ->groupBy($foreignKey);

            $query->leftJoinSub($subQuery, $subAlias, "{$parentTable}.{$localKey}", '=', "{$subAlias}.{$foreignKey}");
            $query->selectRaw(sprintf(
                'COALESCE(%s, 0) as %s',
                $grammar->wrap("{$subAlias}.aggregate_count"),
                $grammar->wrap($columnName),
            ));
        } elseif ($relation instanceof BelongsToMany) {
            $pivotTable = $relation->getTable();
            $foreignPivotKey = $relation->getForeignPivotKeyName();
            $parentKey = $relation->getParentKeyName();

            $subQuery = $query->connection->table($pivotTable)
                ->select($foreignPivotKey)
                ->selectRaw(sprintf('COUNT(*) as %s', $grammar->wrap('aggregate_count')))
                ->groupBy($foreignPivotKey);

            $query->leftJoinSub($subQuery, $subAlias, "{$parentTable}.{$parentKey}", '=', "{$subAlias}.{$foreignPivotKey}");
            $query->selectRaw(sprintf(
                'COALESCE(%s, 0) as %s',
                $grammar->wrap("{$subAlias}.aggregate_count"),
                $grammar->wrap($columnName),
            ));
        } else {
            Log::warning('SmartQuery: Unsupported relation type for raw count include', [
                'relation' => $relationName,
                'type' => get_class($relation),
                'model' => get_class($model),
            ]);
        }
    }

    protected function applyExistsInclude(QueryBuilder $query, string $relationName, $model): void
    {
        if (! method_exists($model, $relationName)) {
            return;
        }

        $relation = $model->{$relationName}();
        $grammar = $query->getGrammar();
        $parentTable = $relation->getParent()->getTable();
        $columnName = $relationName . '_exists';
        $subAlias = $relationName . '_exs';

        if ($relation instanceof HasMany || $relation instanceof HasOne) {
            $relatedTable = $relation->getRelated()->getTable();
            $foreignKey = $relation->getForeignKeyName();
            $localKey = $relation->getLocalKeyName();

            $subQuery = $query->connection->table($relatedTable)
                ->select($foreignKey)
                ->groupBy($foreignKey);

            $query->leftJoinSub($subQuery, $subAlias, "{$parentTable}.{$localKey}", '=', "{$subAlias}.{$foreignKey}");
            $query->selectRaw(sprintf(
                'CASE WHEN %s IS NOT NULL THEN 1 ELSE 0 END as %s',
                $grammar->wrap("{$subAlias}.{$foreignKey}"),
                $grammar->wrap($columnName),
            ));
        } elseif ($relation instanceof BelongsToMany) {
            $pivotTable = $relation->getTable();
            $foreignPivotKey = $relation->getForeignPivotKeyName();
            $parentKey = $relation->getParentKeyName();

            $subQuery = $query->connection->table($pivotTable)
                ->select($foreignPivotKey)
                ->groupBy($foreignPivotKey);

            $query->leftJoinSub($subQuery, $subAlias, "{$parentTable}.{$parentKey}", '=', "{$subAlias}.{$foreignPivotKey}");
            $query->selectRaw(sprintf(
                'CASE WHEN %s IS NOT NULL THEN 1 ELSE 0 END as %s',
                $grammar->wrap("{$subAlias}.{$foreignPivotKey}"),
                $grammar->wrap($columnName),
            ));
        } else {
            Log::warning('SmartQuery: Unsupported relation type for raw exists include', [
                'relation' => $relationName,
                'type' => get_class($relation),
                'model' => get_class($model),
            ]);
        }
    }
}
