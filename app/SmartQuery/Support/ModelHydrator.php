<?php

declare(strict_types=1);

namespace App\SmartQuery\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ModelHydrator
{
    public static function hydrate(\stdClass $result, string $modelClass): Model
    {
        $model = new $modelClass;
        $model->setRawAttributes((array) $result, true);
        $model->exists = true;
        $model->wasRecentlyCreated = false;

        return $model;
    }

    public static function hydrateCollection(Collection $results, string $modelClass): Collection
    {
        return $results->map(fn ($result) => static::hydrate($result, $modelClass));
    }
}
