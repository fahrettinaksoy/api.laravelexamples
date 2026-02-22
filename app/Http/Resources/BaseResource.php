<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\ResponseReference;
use App\Traits\HasFieldSelection;
use App\Traits\HasRelationshipSeparation;
use App\Traits\HasResourceLinks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    use HasFieldSelection;
    use HasRelationshipSeparation;
    use HasResourceLinks;

    protected string $responseMessage = 'İşlem başarılı';

    protected int $responseStatusCode = 200;

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    public function resolve($request = null): array
    {
        $resolved = parent::resolve($request);
        $request = $request ?: request();

        if (! $this->resource instanceof Model) {
            return $resolved;
        }

        [$attributes, $relationships] = $this->separateAttributesAndRelationships($resolved);

        $attributes = $this->applyFieldSelection($attributes, $request);

        $data = [
            'type' => $this->resolveResourceType(),
            'id' => (string) $this->resolveResourceId(),
            'attributes' => $attributes,
            'links' => [
                'self' => $this->resolveSelfLink(),
            ],
        ];

        if (! empty($relationships)) {
            $data['relationships'] = $relationships;
        }

        return $data;
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

    public function response($request = null): JsonResponse
    {
        return parent::response($request)->setStatusCode($this->responseStatusCode);
    }
}
