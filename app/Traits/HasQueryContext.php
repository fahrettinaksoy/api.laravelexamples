<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Request;

trait HasQueryContext
{
    protected function buildQueryContext(Request $request): array
    {
        return array_filter([
            'filter' => $request->input('filter'),
            'sort' => $request->input('sort'),
            'fields' => $request->input('fields'),
            'include' => $request->input('include'),
            'page' => $request->input('page'),
            'limit' => $request->input('limit'),
            'per_page' => $request->input('per_page'),
        ], fn (mixed $value): bool => $value !== null);
    }

    protected function parseIncludes(Request $request): array
    {
        $include = $request->input('include');

        if (! $include) {
            return [];
        }

        return array_map('trim', explode(',', $include));
    }
}
