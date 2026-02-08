<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

abstract class BaseException extends Exception
{
    protected int $statusCode = 500;

    protected string $errorCode = 'INTERNAL_SERVER_ERROR';

    protected array $context = [];

    public function __construct(
        string $message,
        ?int $statusCode = null,
        ?string $errorCode = null,
        array $context = []
    ) {
        parent::__construct($message);

        if ($statusCode !== null) {
            $this->statusCode = $statusCode;
        }

        if ($errorCode !== null) {
            $this->errorCode = $errorCode;
        }

        $this->context = $context;

        $this->logException();
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode,
            'meta' => $this->buildMeta(),
        ], $this->statusCode);
    }

    protected function buildMeta(): array
    {
        $meta = [
            'timestamp' => now()->toIso8601String(),
            'version' => 'v1',
        ];

        if (config('app.debug') && ! empty($this->context)) {
            $meta['context'] = $this->context;
        }

        return $meta;
    }

    protected function logException(): void
    {
        Log::error($this->getMessage(), [
            'error_code' => $this->errorCode,
            'status_code' => $this->statusCode,
            'context' => $this->context,
            'trace' => $this->getTraceAsString(),
        ]);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
