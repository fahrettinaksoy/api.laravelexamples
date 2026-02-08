<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Includes;

class CustomInclude implements IncludeInterface
{
    public function __construct(
        protected IncludeInterface $include
    ) {}

    public function __invoke($query, string $include): void
    {
        ($this->include)($query, $include);
    }
}
