<?php

declare(strict_types=1);

namespace App\SmartQuery\Concerns;

use App\SmartQuery\Builders\Fields\AllowedField;
use App\SmartQuery\Exceptions\InvalidFieldQuery;
use App\SmartQuery\SmartQueryRequest;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * SelectsFields Trait
 *
 * Adds sparse fieldset capabilities to SmartQuery.
 * Ana tablo ve ilişki tabloları için field selection destekler.
 */
trait SelectsFields
{
    protected array $allowedFields = [];

    /**
     * Set allowed fields
     *
     * @param  array|string  $fields
     * @return $this
     */
    public function allowedFields($fields): static
    {
        $fields = is_array($fields) ? $fields : func_get_args();

        $this->allowedFields = collect($fields)->map(function ($field) {
            // String field name
            if (is_string($field)) {
                return new AllowedField($field);
            }

            // AllowedField instance
            if ($field instanceof AllowedField) {
                return $field;
            }

            throw new \InvalidArgumentException(
                'Field must be a string or AllowedField instance',
            );
        })->toArray();

        $this->applyFields();

        return $this;
    }

    /**
     * Apply fields from request
     */
    protected function applyFields(): void
    {
        $requestedFields = $this->getRequestedFields();

        if (empty($requestedFields)) {
            return;
        }

        // Group fields by table/resource
        $fieldsByTable = $this->groupFieldsByTable($requestedFields);

        foreach ($fieldsByTable as $table => $fields) {
            $this->applyFieldsForTable($table, $fields);
        }
    }

    /**
     * Get requested fields from request
     */
    protected function getRequestedFields(): array
    {
        $fieldsParam = $this->request->input('fields', []);

        if (empty($fieldsParam) || ! is_array($fieldsParam)) {
            return [];
        }

        return $fieldsParam;
    }

    /**
     * Group fields by table
     */
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

    /**
     * Apply fields for a specific table
     */
    protected function applyFieldsForTable(string $table, array $fields): void
    {
        // Validate fields
        foreach ($fields as $field) {
            $fullFieldName = "{$table}.{$field}";

            if (! $this->isFieldAllowed($fullFieldName)) {
                if (config('smartquery.throw_on_invalid_field', true)) {
                    throw InvalidFieldQuery::fieldNotAllowed(
                        $fullFieldName,
                        $this->getAllowedFieldNames(),
                    );
                }

                return;
            }
        }

        // Apply field selection
        if ($this->isMainTable($table)) {
            $this->builder->select(array_map(fn ($f) => "{$table}.{$f}", $fields));
        } elseif ($this->builder instanceof EloquentBuilder && isset($this->model)) {
            $this->applyRelationshipFieldSelection($table, $fields);
        }
    }

    /**
     * İlişki tablosu için field selection uygular.
     * with() closure kullanarak sadece istenen alanları + foreign key'i seçer.
     */
    protected function applyRelationshipFieldSelection(string $table, array $fields): void
    {
        $relationName = $this->findRelationForTable($table);

        if ($relationName === null) {
            return;
        }

        $relation = $this->model->{$relationName}();
        $foreignKey = $this->getRelationForeignKey($relation);

        // Foreign key her zaman dahil edilmeli - aksi halde ilişki eşleşmez
        $selectFields = array_values(array_unique(
            array_merge($fields, array_filter([$foreignKey])),
        ));

        $this->builder->with([$relationName => function ($query) use ($selectFields) {
            $query->select($selectFields);
        }]);
    }

    /**
     * Verilen tablo adını kullanan ilişkiyi bulur.
     * allowedIncludes üzerinden arar (IncludesRelationships trait'inden gelir).
     */
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

    /**
     * İlişkinin foreign key adını döndürür.
     */
    protected function getRelationForeignKey(Relation $relation): ?string
    {
        if (method_exists($relation, 'getForeignKeyName')) {
            return $relation->getForeignKeyName();
        }

        return null;
    }

    /**
     * Check if table is the main query table
     */
    protected function isMainTable(string $table): bool
    {
        if (isset($this->model)) {
            return $table === $this->model->getTable();
        }

        return false;
    }

    /**
     * Check if field is allowed
     */
    protected function isFieldAllowed(string $fieldName): bool
    {
        foreach ($this->allowedFields as $field) {
            if ($field->getName() === $fieldName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all allowed field names
     */
    protected function getAllowedFieldNames(): array
    {
        return array_map(
            fn (AllowedField $field) => $field->getName(),
            $this->allowedFields,
        );
    }
}
