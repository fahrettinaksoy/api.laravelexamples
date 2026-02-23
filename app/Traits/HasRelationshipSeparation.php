<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasRelationshipSeparation
{
    protected function separateAttributesAndRelationships(array $data): array
    {
        $relationKeys = $this->getLoadedRelationKeys();

        $attributes = [];
        $relationships = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $relationKeys, true)) {
                $relationships[$key] = $value;
            } else {
                $attributes[$key] = $value;
            }
        }

        return [$attributes, $relationships];
    }

    protected function getLoadedRelationKeys(): array
    {
        if (! $this->resource instanceof Model) {
            return [];
        }

        $relations = array_keys($this->resource->getRelations());
        $snakeRelations = array_map(
            static fn (string $relation): string => Str::snake($relation),
            $relations,
        );

        return array_values(array_unique(array_merge($relations, $snakeRelations)));
    }
}
