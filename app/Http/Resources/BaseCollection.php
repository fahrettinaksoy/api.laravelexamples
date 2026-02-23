<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\ResponseReference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
{
    protected string $responseMessage = '';

    protected int $responseStatusCode = 200;

    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
    }

    public function paginationInformation($request, $paginated, $default): array
    {
        return [
            'meta' => [
                'current_page' => $paginated['current_page'],
                'last_page' => $paginated['last_page'],
                'per_page' => $paginated['per_page'],
                'total' => $paginated['total'],
                'from' => $paginated['from'],
                'to' => $paginated['to'],
            ],
            'links' => [
                'first' => $paginated['first_page_url'] ?? null,
                'last' => $paginated['last_page_url'] ?? null,
                'prev' => $paginated['prev_page_url'] ?? null,
                'next' => $paginated['next_page_url'] ?? null,
            ],
        ];
    }

    public function with($request): array
    {
        return [
            'success' => true,
            'reference' => app(ResponseReference::class)->build(
                $this->responseMessage ?: __('api.success'),
                $this->responseStatusCode,
            ),
        ];
    }

    public function withMessage(string $message): static
    {
        $this->responseMessage = $message;

        return $this;
    }

    public function withStatusCode(int $statusCode): static
    {
        $this->responseStatusCode = $statusCode;

        return $this;
    }

    public function response($request = null): JsonResponse
    {
        return parent::response($request)->setStatusCode($this->responseStatusCode);
    }
}
