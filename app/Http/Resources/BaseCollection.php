<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\ResponseReference;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
{
    protected string $responseMessage = 'İşlem başarılı';

    protected int $responseStatusCode = 200;

    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
    }

    public function with($request): array
    {
        return [
            'success' => true,
            'reference' => ResponseReference::build($this->responseMessage, $this->responseStatusCode),
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
}
