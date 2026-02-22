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

    private function applyFieldMetadata(): void
    {
        $class = static::class;

        if (isset(self::$fieldMetadataCache[$class])) {
            $cached = self::$fieldMetadataCache[$class];

            foreach ($cached as $key => $value) {
                $this->{$key} = $value;
            }

            return;
        }

        $dtoClass = static::$fieldSource;
        $metadata = [];

        $metadata['fillable'] = MetadataResolver::fieldsForActions($dtoClass, 'store', 'update');
        $this->fillable = $metadata['fillable'];

        if (property_exists($this, 'allowedFiltering')) {
            $metadata['allowedFiltering'] = MetadataResolver::tableFieldsFor($dtoClass, 'filtering');
            $metadata['allowedSorting'] = MetadataResolver::tableFieldsFor($dtoClass, 'sorting');
            $metadata['allowedShowing'] = MetadataResolver::tableFieldsFor($dtoClass, 'showing');

            $this->allowedFiltering = $metadata['allowedFiltering'];
            $this->allowedSorting = $metadata['allowedSorting'];
            $this->allowedShowing = $metadata['allowedShowing'];

            $this->appendAuditFields();

            $metadata['allowedFiltering'] = $this->allowedFiltering;
            $metadata['allowedSorting'] = $this->allowedSorting;
            $metadata['allowedShowing'] = $this->allowedShowing;
        }

        self::$fieldMetadataCache[$class] = $metadata;
    }

    private function appendAuditFields(): void
    {
        $timestampFields = ['created_at', 'updated_at'];
        $auditFields = ['created_by', 'updated_by'];

        $this->allowedSorting = array_values(array_unique(
            array_merge($this->allowedSorting, $timestampFields),
        ));

        $this->allowedFiltering = array_values(array_unique(
            array_merge($this->allowedFiltering, $timestampFields),
        ));

        $this->allowedShowing = array_values(array_unique(
            array_merge($this->allowedShowing, $timestampFields, $auditFields),
        ));
    }

    public static function fieldSchema(): array
    {
        if (static::$fieldSource === null) {
            return [];
        }

        return MetadataResolver::toFieldSchema(static::$fieldSource);
    }
}
