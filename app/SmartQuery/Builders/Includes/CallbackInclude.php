<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Includes;

use Closure;

class CallbackInclude implements IncludeInterface
{
    public function __construct(
        protected Closure $callback,
    ) {}

    public function __invoke($query, string $include): void
    {
        call_user_func($this->callback, $query, $include);
    }
}
