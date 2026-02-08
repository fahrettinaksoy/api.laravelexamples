<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    public function with(Request $request): array
    {
        return [
            'meta' => $this->buildMeta(),
        ];
    }

    protected function buildMeta(): array
    {
        return [
            'timestamp' => now()->toIso8601String(),
            'version' => 'v1',
        ];
    }
}
