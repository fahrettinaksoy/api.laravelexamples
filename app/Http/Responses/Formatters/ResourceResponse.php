<?php

declare(strict_types=1);

namespace App\Http\Responses\Formatters;

use App\Http\Responses\Contracts\ResponseInterface;
use Illuminate\Http\JsonResponse;

class ResourceResponse implements ResponseInterface
{
    public function format(mixed ...$args): JsonResponse
    {
        [$data, $resourceClass, $message, $status] = $this->extractArguments($args);

        $resource = new $resourceClass($data);

        return response()->json([
            'success' => true,
            'data' => $resource,
            'message' => $message,
            'meta' => $this->buildMeta(),
        ], $status);
    }

    private function extractArguments(array $args): array
    {
        return [
            $args[0],
            $args[1],
            $args[2] ?? 'İşlem başarılı',
            $args[3] ?? 200,
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
