<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Resolved resource verisini attributes ve relationships olarak ayirir.
 * Model uzerindeki yuklenmis iliskileri tespit ederek JSON:API yapisina uygun ayristirma saglar.
 *
 * @mixin \Illuminate\Http\Resources\Json\JsonResource
 */
trait HasRelationshipSeparation
{
    /**
     * Resolved veriyi attributes ve relationships olarak ikiye ayirir.
     *
     * @param  array<string, mixed>  $data
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
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

    /**
     * Model uzerindeki yuklenmis iliskilerin key listesini dondurur.
     * Hem camelCase hem snake_case varyantlarini icerir.
     *
     * @return array<int, string>
     */
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
