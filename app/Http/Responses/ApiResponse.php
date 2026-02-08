<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Http\Responses\Formatters\CollectionResponse;
use App\Http\Responses\Formatters\ErrorResponse;
use App\Http\Responses\Formatters\PaginatedResponse;
use App\Http\Responses\Formatters\ResourceResponse;
use App\Http\Responses\Formatters\SuccessResponse;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public function __construct(
        private readonly SuccessResponse $successResponse,
        private readonly ErrorResponse $errorResponse,
        private readonly ResourceResponse $resourceResponse,
        private readonly CollectionResponse $collectionResponse,
        private readonly PaginatedResponse $paginatedResponse,
    ) {}

    public function success(string $message = 'İşlem başarılı', int $status = 200): JsonResponse
    {
        return $this->successResponse->format($message, $status);
    }

    public function error(
        string $message,
        int $status = 400,
        ?string $errorCode = null
    ): JsonResponse {
        return $this->errorResponse->format($message, $status, $errorCode);
    }

    public function resource(
        mixed $data,
        string $resourceClass,
        string $message = 'İşlem başarılı',
        int $status = 200
    ): JsonResponse {
        return $this->resourceResponse->format($data, $resourceClass, $message, $status);
    }

    public function collection(
        mixed $data,
        string $collectionClass,
        string $message = 'İşlem başarılı',
        int $status = 200
    ): JsonResponse {
        return $this->collectionResponse->format($data, $collectionClass, $message, $status);
    }

    public function paginated(
        mixed $data,
        string $collectionClass,
        string $message = 'İşlem başarılı',
        int $status = 200
    ): JsonResponse {
        return $this->paginatedResponse->format($data, $collectionClass, $message, $status);
    }
}
