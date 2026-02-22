<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Sorts;

use Closure;

class CallbackSort extends Sort
{
    public function __construct(
        protected Closure $callback,
    ) {}

    public function __invoke($query, bool $descending, string $property): void
    {
        call_user_func($this->callback, $query, $descending, $property);
    }
}
