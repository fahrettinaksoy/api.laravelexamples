<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Resource attribute'larini request'teki fields parametresine gore filtreler.
 * Sparse fieldset destegi saglar (ornek: ?fields[products]=name,price).
 *
 * @mixin \Illuminate\Http\Resources\Json\JsonResource
 */
trait HasFieldSelection
{
    /**
     * Istenen alanlara gore attribute'lari filtreler.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function applyFieldSelection(array $attributes, mixed $request): array
    {
        $requestedFields = $request->input('fields', []);

        if (empty($requestedFields) || ! is_array($requestedFields)) {
            return $attributes;
        }

        if (! $this->resource instanceof Model) {
            return $attributes;
        }

        $tableName = $this->resource->getTable();

        if (! isset($requestedFields[$tableName])) {
            return $attributes;
        }

        $fields = is_string($requestedFields[$tableName])
            ? explode(',', $requestedFields[$tableName])
            : (array) $requestedFields[$tableName];

        return array_filter(
            $attributes,
            static fn (string $key): bool => in_array($key, $fields, true),
            ARRAY_FILTER_USE_KEY,
        );
    }
}
