<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Includes;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class CountInclude implements IncludeInterface
{
    public function __invoke($query, string $include): void
    {
        if ($query instanceof EloquentBuilder) {
            $query->withCount($include);
        }
    }
}
