<?php

declare(strict_types=1);

namespace App\Http\Responses\Formatters;

use App\Http\Responses\Contracts\ResponseInterface;
use Illuminate\Http\JsonResponse;

class PaginatedResponse implements ResponseInterface
{
    public function format(mixed ...$args): JsonResponse
    {
        [$data, $collectionClass, $message, $status] = $this->extractArguments($args);

        $collection = new $collectionClass($data);

        return response()->json([
            'success' => true,
            'data' => $collection,
            'message' => $message,
            'meta' => $this->buildMeta([
                'pagination' => [
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ],
            ]),
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
