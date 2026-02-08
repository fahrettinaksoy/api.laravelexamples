<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => $this->buildMeta(),
        ];
    }

    protected function buildMeta(): array
    {
        $meta = [
            'timestamp' => now()->toIso8601String(),
            'version' => 'v1',
        ];

        if ($this->resource instanceof LengthAwarePaginator) {
            $meta['pagination'] = [
                'total' => $this->resource->total(),
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'from' => $this->resource->firstItem(),
                'to' => $this->resource->lastItem(),
            ];
        }

        return $meta;
    }
}
