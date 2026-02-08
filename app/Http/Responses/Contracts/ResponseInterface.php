<?php

declare(strict_types=1);

namespace App\Http\Responses\Contracts;

use Illuminate\Http\JsonResponse;

interface ResponseInterface
{
    public function format(mixed ...$args): JsonResponse;
}
