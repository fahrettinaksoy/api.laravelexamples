<?php

declare(strict_types=1);

namespace App\SmartQuery\Concerns;

use App\SmartQuery\Builders\Fields\AllowedField;
use App\SmartQuery\Exceptions\InvalidFieldQuery;
use App\SmartQuery\SmartQueryRequest;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * SelectsFields Trait
 *
 * Adds sparse fieldset capabilities to SmartQuery
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
                'Field must be a string or AllowedField instance'
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
                        $this->getAllowedFieldNames()
                    );
                }

                return;
            }
        }

        // Apply field selection
        if ($this->isMainTable($table)) {
            // Main table - use select()
            $this->builder->select(array_map(fn ($f) => "{$table}.{$f}", $fields));
        } else {
            // Relationship table - modify with() closure
            if ($this->builder instanceof EloquentBuilder) {
                // This requires modifying the with() calls which is complex
                // For now, we'll skip relationship field selection
                // Full implementation would require tracking with() calls
            }
        }
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
            $this->allowedFields
        );
    }
}
