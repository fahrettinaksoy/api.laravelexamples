<?php

declare(strict_types=1);

namespace App\Traits;

use App\SmartQuery\SmartQuery;
use Illuminate\Support\Facades\Log;

trait HasSmartQuery
{
    protected function buildSmartQuery(): SmartQuery
    {
        $query = SmartQuery::for($this->model)
            ->allowedFilters($this->resolveAllowedFilters())
            ->allowedSorts($this->model->getAllowedSorting())
            ->allowedIncludes($this->model->getAllowedRelations())
            ->defaultSort($this->model->getDefaultSorting());

        $defaultRelations = $this->model->getDefaultRelations();

        if (! empty($defaultRelations)) {
            $query->with($defaultRelations);
        }

        $allowedFields = $this->resolveAllowedFields();

        if (! empty($allowedFields)) {
            $query->allowedFields($allowedFields);
        }

        return $query;
    }

    protected function resolveAllowedFilters(): array
    {
        if (method_exists($this->model, 'getAllowedFilters')) {
            return $this->model->getAllowedFilters();
        }

        return $this->model->getAllowedFiltering();
    }

    protected function resolveAllowedFields(): array
    {
        $fields = [];

        $allowedShowing = $this->model->getAllowedShowing();

        if (! empty($allowedShowing)) {
            $tableName = $this->model->getTable();

            foreach ($allowedShowing as $field) {
                $fields[] = "{$tableName}.{$field}";
            }
        }

        foreach ($this->model->getAllowedRelations() as $relationName) {
            if (! method_exists($this->model, $relationName)) {
                continue;
            }

            try {
                $relation = $this->model->{$relationName}();
                $relatedModel = $relation->getRelated();

                $relatedShowing = $relatedModel->getAllowedShowing();

                if (! empty($relatedShowing)) {
                    $relatedTable = $relatedModel->getTable();

                    foreach ($relatedShowing as $field) {
                        $fields[] = "{$relatedTable}.{$field}";
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('SmartQuery: Failed to resolve fields for relation', [
                    'model' => get_class($this->model),
                    'relation' => $relationName,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }
        }

        return array_values(array_unique($fields));
    }

    protected function resolveIncludes(array $includes = []): array
    {
        $allowed = $this->model->getAllowedRelations();
        $defaults = $this->model->getDefaultRelations();

        if (empty($includes)) {
            return $defaults;
        }

        $resolved = array_values(array_intersect($includes, $allowed));

        return array_values(array_unique(array_merge($defaults, $resolved)));
    }
}
