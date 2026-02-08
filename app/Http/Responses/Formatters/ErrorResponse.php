<?php

declare(strict_types=1);

namespace App\Http\Responses\Formatters;

use App\Http\Responses\Contracts\ResponseInterface;
use Illuminate\Http\JsonResponse;

class ErrorResponse implements ResponseInterface
{
    public function format(mixed ...$args): JsonResponse
    {
        [$message, $status, $errorCode] = $this->extractArguments($args);

        $response = [
            'success' => false,
            'message' => $message,
            'meta' => $this->buildMeta(),
        ];

        if ($errorCode !== null) {
            $response['error_code'] = $errorCode;
        }

        return response()->json($response, $status);
    }

    private function extractArguments(array $args): array
    {
        return [
            $args[0] ?? 'İşlem başarısız',
            $args[1] ?? 400,
            $args[2] ?? null,
        ];
    }

    private function buildMeta(): array
    {
        $additional = [];

        return array_merge([
            'timestamp' => now()->toIso8601String(),
            'version' => 'v1',
        ], $additional);
    }
}
