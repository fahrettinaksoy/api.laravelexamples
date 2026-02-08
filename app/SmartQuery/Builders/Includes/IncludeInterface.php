<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Includes;

interface IncludeInterface
{
    public function __invoke($query, string $include): void;
}
