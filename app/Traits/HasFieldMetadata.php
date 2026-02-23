<?php

declare(strict_types=1);

namespace App\Traits;

use App\Support\MetadataResolver;

trait HasFieldMetadata
{
    private static array $fieldMetadataCache = [];

    public function initializeHasFieldMetadata(): void
    {
        if (static::$fieldSource !== null) {
            $this->applyFieldMetadata();
        }
    }

    protected function hasSmartQueryFields(): bool
    {
        return in_array(HasSmartQueryConfig::class, class_uses_recursive(static::class), true);
    }

    protected function auditTimestampFields(): array
    {
        return ['created_at', 'updated_at'];
    }

    protected function auditUserFields(): array
    {
        return ['created_by', 'updated_by'];
    }

    private function applyFieldMetadata(): void
    {
        $class = static::class;

        if (isset(self::$fieldMetadataCache[$class])) {
            foreach (self::$fieldMetadataCache[$class] as $key => $value) {
                $this->{$key} = $value;
            }

            return;
        }

        $dtoClass = static::$fieldSource;
        $metadata = $this->resolveMetadataFromDto($dtoClass);

        self::$fieldMetadataCache[$class] = $metadata;
    }

    private function resolveMetadataFromDto(string $dtoClass): array
    {
        $metadata = [];

        $metadata['fillable'] = MetadataResolver::fieldsForActions($dtoClass, 'store', 'update');
        $this->fillable = $metadata['fillable'];

        if ($this->hasSmartQueryFields()) {
            $metadata = array_merge($metadata, $this->resolveSmartQueryFields($dtoClass));
        }

        return $metadata;
    }

    private function resolveSmartQueryFields(string $dtoClass): array
    {
        $timestampFields = $this->auditTimestampFields();
        $auditFields = $this->auditUserFields();

        $filtering = MetadataResolver::tableFieldsFor($dtoClass, 'filtering');
        $sorting = MetadataResolver::tableFieldsFor($dtoClass, 'sorting');
        $showing = MetadataResolver::tableFieldsFor($dtoClass, 'showing');

        $this->allowedFiltering = array_values(array_unique(
            array_merge($filtering, $timestampFields),
        ));

        $this->allowedSorting = array_values(array_unique(
            array_merge($sorting, $timestampFields),
        ));

        $this->allowedShowing = array_values(array_unique(
            array_merge($showing, $timestampFields, $auditFields),
        ));

        return [
            'allowedFiltering' => $this->allowedFiltering,
            'allowedSorting' => $this->allowedSorting,
            'allowedShowing' => $this->allowedShowing,
        ];
    }

    public static function fieldSchema(): array
    {
        if (static::$fieldSource === null) {
            return [];
        }

        return MetadataResolver::toFieldSchema(static::$fieldSource);
    }
}
