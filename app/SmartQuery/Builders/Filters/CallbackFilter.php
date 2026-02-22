<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

use Closure;

class CallbackFilter extends Filter
{
    public function __construct(
        protected Closure $callback,
    ) {}

    public function __invoke($query, $value, string $property)
    {
        return call_user_func($this->callback, $query, $value, $property);
    }
}
