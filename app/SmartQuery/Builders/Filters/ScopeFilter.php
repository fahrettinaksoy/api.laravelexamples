<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

use App\SmartQuery\Support\QueryScopes;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ScopeFilter extends Filter
{
    public function __construct(
        protected ?string $scopeName = null
    ) {}

    public function __invoke($query, $value, string $property)
    {
        $scopeName = $this->scopeName ?? $property;

        if ($query instanceof EloquentBuilder) {
            $parameters = is_array($value) ? $value : [$value];

            return $query->{$scopeName}(...$parameters);
        }

        $table = $query->from;

        if (QueryScopes::has($table, $scopeName)) {
            QueryScopes::apply($query, $table, $scopeName, $value);

            return $query;
        }

        return $query;
    }
}
