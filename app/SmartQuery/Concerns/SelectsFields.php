<?php

declare(strict_types=1);

namespace App\SmartQuery\Concerns;

use App\SmartQuery\Builders\Fields\AllowedField;
use App\SmartQuery\Exceptions\InvalidFieldQuery;
use App\SmartQuery\SmartQueryRequest;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;

trait SelectsFields
{
    protected array $allowedFields = [];

    public function allowedFields($fields): static
    {
        $fields = is_array($fields) ? $fields : func_get_args();

        $normalized = [];

        foreach ($fields as $field) {
            if (is_string($field)) {
                $field = new AllowedField($field);
            } elseif (! $field instanceof AllowedField) {
                throw new \InvalidArgumentException(
                    'Field must be a string or AllowedField instance',
                );
            }

            $normalized[$field->getName()] = $field;
        }

        $this->allowedFields = $normalized;

        $this->applyFields();

        return $this;
    }

    protected function applyFields(): void
    {
        $requestedFields = $this->getRequestedFields();

        if (empty($requestedFields)) {
            return;
        }

        $fieldsByTable = $this->groupFieldsByTable($requestedFields);

        foreach ($fieldsByTable as $table => $fields) {
            $this->applyFieldsForTable($table, $fields);
        }
    }

    protected function getRequestedFields(): array
    {
        $fieldsParam = $this->request->input('fields', []);

        if (empty($fieldsParam) || ! is_array($fieldsParam)) {
            return [];
        }

        return $fieldsParam;
    }

    protected function groupFieldsByTable(array $requestedFields): array
    {
        $grouped = [];

        foreach ($requestedFields as $table => $fields) {
            $delimiter = SmartQueryRequest::getFieldsArrayValueDelimiter();

            if (is_string($fields)) {
                $fields = explode($delimiter, $fields);
            }

            $grouped[$table] = $fields;
        }

        return $grouped;
    }

    protected function applyFieldsForTable(string $table, array $fields): void
    {
        $validFields = [];

        foreach ($fields as $field) {
            $fullFieldName = "{$table}.{$field}";

            if (! $this->isFieldAllowed($fullFieldName)) {
                if (config('smartquery.throw_on_invalid_field', true)) {
                    throw InvalidFieldQuery::fieldNotAllowed(
                        $fullFieldName,
                        $this->getAllowedFieldNames(),
                    );
                }

                continue;
            }

            $validFields[] = $field;
        }

        if (empty($validFields)) {
            return;
        }

        if ($this->isMainTable($table)) {
            $primaryKey = isset($this->model) ? $this->model->getKeyName() : null;
            $fieldsWithPK = $validFields;

            if ($primaryKey && ! in_array($primaryKey, $fieldsWithPK, true)) {
                array_unshift($fieldsWithPK, $primaryKey);
            }

            $qualifiedFields = array_map(fn ($f) => "{$table}.{$f}", $fieldsWithPK);

            $existingColumns = $this->builder instanceof EloquentBuilder
                ? $this->builder->getQuery()->columns
                : ($this->builder->columns ?? null);

            if (! empty($existingColumns)) {
                $this->builder->addSelect($qualifiedFields);
            } else {
                $this->builder->select($qualifiedFields);
            }
        } elseif ($this->builder instanceof EloquentBuilder && isset($this->model)) {
            $this->applyRelationshipFieldSelection($table, $validFields);
        }
    }

    protected function applyRelationshipFieldSelection(string $table, array $fields): void
    {
        $relationName = $this->findRelationForTable($table);

        if ($relationName === null) {
            return;
        }

        $relation = $this->model->{$relationName}();
        $foreignKey = $this->getRelationForeignKey($relation);

        $selectFields = array_values(array_unique(
            array_merge($fields, array_filter([$foreignKey])),
        ));

        $this->builder->with([$relationName => function ($query) use ($selectFields) {
            $query->select($selectFields);
        }]);
    }

    protected function findRelationForTable(string $table): ?string
    {
        if (! isset($this->model) || ! property_exists($this, 'allowedIncludes')) {
            return null;
        }

        foreach ($this->allowedIncludes as $include) {
            $internalName = $include->getInternalName();

            if (! method_exists($this->model, $internalName)) {
                continue;
            }

            try {
                $relation = $this->model->{$internalName}();

                if ($relation instanceof Relation && $relation->getRelated()->getTable() === $table) {
                    return $internalName;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    protected function getRelationForeignKey(Relation $relation): ?string
    {
        if (method_exists($relation, 'getForeignKeyName')) {
            return $relation->getForeignKeyName();
        }

        return null;
    }

    protected function isMainTable(string $table): bool
    {
        if (isset($this->model)) {
            return $table === $this->model->getTable();
        }

        return false;
    }

    protected function isFieldAllowed(string $fieldName): bool
    {
        return isset($this->allowedFields[$fieldName]);
    }

    protected function getAllowedFieldNames(): array
    {
        return array_keys($this->allowedFields);
    }
}
