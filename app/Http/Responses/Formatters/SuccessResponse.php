<?php

declare(strict_types=1);

namespace App\Http\Responses\Formatters;

use App\Http\Responses\Contracts\ResponseInterface;
use Illuminate\Http\JsonResponse;

class SuccessResponse implements ResponseInterface
{
    public function format(mixed ...$args): JsonResponse
    {
        [$message, $status] = $this->extractArguments($args);

        return response()->json([
            'success' => true,
            'message' => $message,
            'meta' => $this->buildMeta(),
        ], $status);
    }

    private function extractArguments(array $args): array
    {
        return [
            $args[0] ?? 'İşlem başarılı',
            $args[1] ?? 200,
        ];
    }

    private function buildMeta(array $additional = []): array
    {
        return array_merge([
            'timestamp' => now()->toIso8601String(),
            'version' => 'v1',
        ], $additional);
    }
}
