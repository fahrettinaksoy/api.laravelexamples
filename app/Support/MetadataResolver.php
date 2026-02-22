<?php

declare(strict_types=1);

namespace App\Support;

use App\Attributes\Model\ActionType;
use App\Attributes\Model\FormField;
use App\Attributes\Model\TableColumn;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

class MetadataResolver
{
    private static array $metadataCache = [];

    private static array $fieldsCache = [];

    private static array $tableFieldsCache = [];

    public static function resolve(string $dtoClass): array
    {
        if (isset(self::$metadataCache[$dtoClass])) {
            return self::$metadataCache[$dtoClass];
        }

        $reflection = new ReflectionClass($dtoClass);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            self::$metadataCache[$dtoClass] = [];

            return [];
        }

        $metadata = [];

        foreach ($constructor->getParameters() as $param) {
            $metadata[$param->getName()] = self::extractParameterMetadata($param);
        }

        self::$metadataCache[$dtoClass] = $metadata;

        return $metadata;
    }

    public static function fieldsForActions(string $dtoClass, string ...$actions): array
    {
        $cacheKey = $dtoClass . ':' . implode(',', $actions);

        if (isset(self::$fieldsCache[$cacheKey])) {
            return self::$fieldsCache[$cacheKey];
        }

        $metadata = self::resolve($dtoClass);
        $fields = [];

        foreach ($metadata as $field) {
            if (! empty(array_intersect($actions, $field['actions']))) {
                $fields[] = $field['name'];
            }
        }

        self::$fieldsCache[$cacheKey] = $fields;

        return $fields;
    }

    public static function tableFieldsFor(string $dtoClass, string $tableAction): array
    {
        $cacheKey = $dtoClass . ':' . $tableAction;

        if (isset(self::$tableFieldsCache[$cacheKey])) {
            return self::$tableFieldsCache[$cacheKey];
        }

        $metadata = self::resolve($dtoClass);
        $fields = [];

        foreach ($metadata as $field) {
            if ($field['table'] !== null && in_array($tableAction, $field['table']['actions'], true)) {
                $fields[] = $field['name'];
            }
        }

        self::$tableFieldsCache[$cacheKey] = $fields;

        return $fields;
    }

    public static function toFieldSchema(string $dtoClass): array
    {
        $metadata = self::resolve($dtoClass);
        $schema = [];

        foreach ($metadata as $field) {
            $schema[$field['name']] = [
                'type' => $field['phpType'],
                'nullable' => $field['nullable'],
                'form' => $field['form'],
                'table' => $field['table'],
                'actions' => $field['actions'],
            ];
        }

        return $schema;
    }

    private static function extractParameterMetadata(ReflectionParameter $param): array
    {
        $type = $param->getType();
        $phpType = $type instanceof ReflectionNamedType ? $type->getName() : 'mixed';

        return [
            'name' => $param->getName(),
            'phpType' => $phpType,
            'nullable' => $type instanceof ReflectionNamedType && $type->allowsNull(),
            'actions' => self::extractActionTypes($param),
            'form' => self::extractFormField($param),
            'table' => self::extractTableColumn($param),
        ];
    }

    private static function extractActionTypes(ReflectionParameter $param): array
    {
        $attributes = $param->getAttributes(ActionType::class);

        if (empty($attributes)) {
            return [];
        }

        return $attributes[0]->newInstance()->actions;
    }

    private static function extractFormField(ReflectionParameter $param): ?array
    {
        $attributes = $param->getAttributes(FormField::class);

        if (empty($attributes)) {
            return null;
        }

        $instance = $attributes[0]->newInstance();

        return [
            'type' => $instance->type,
            'sort_order' => $instance->sort_order,
            'options' => $instance->options,
            'default' => $instance->default,
            'relationship' => $instance->relationship,
        ];
    }

    private static function extractTableColumn(ReflectionParameter $param): ?array
    {
        $attributes = $param->getAttributes(TableColumn::class);

        if (empty($attributes)) {
            return null;
        }

        $instance = $attributes[0]->newInstance();

        return [
            'actions' => $instance->actions,
            'sorting' => $instance->sorting,
            'primaryKey' => $instance->primaryKey,
        ];
    }
}
