<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrashedFilter extends Filter
{
    public function __invoke($query, $value, string $property)
    {
        if (! $query instanceof EloquentBuilder) {
            return $query;
        }

        if (! in_array(SoftDeletes::class, class_uses_recursive($query->getModel()))) {
            return $query;
        }

        $value = strtolower((string) $value);

        return match ($value) {
            'with', 'true', '1' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            'false', '0', 'without' => $query->withoutTrashed(),
            default => $query,
        };
    }
}
