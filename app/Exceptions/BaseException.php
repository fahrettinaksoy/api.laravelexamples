<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Support\ResponseReference;
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
        array $context = [],
    ) {
        parent::__construct($message);

        if ($statusCode !== null) {
            $this->statusCode = $statusCode;
        }

        if ($errorCode !== null) {
            $this->errorCode = $errorCode;
        }

        $this->context = $context;
    }

    public function render(): JsonResponse
    {
        $this->logException();

        $response = [
            'success' => false,
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode,
        ];

        if (! empty($this->context)) {
            $response['errors'] = $this->context;
        }

        $response['reference'] = ResponseReference::build(
            $this->getMessage(),
            $this->statusCode,
            $this->buildDebug(),
        );

        return response()->json($response, $this->statusCode);
    }

    private function buildDebug(): array
    {
        if (! config('app.debug')) {
            return [];
        }

        return [
            'exception' => static::class,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }

    protected function logException(): void
    {
        $logContext = [
            'error_code' => $this->errorCode,
            'status_code' => $this->statusCode,
            'context' => $this->context,
        ];

        if (config('app.debug')) {
            $logContext['trace'] = $this->getTraceAsString();
        }

        if ($this->statusCode >= 500) {
            Log::error($this->getMessage(), $logContext);
        } else {
            Log::warning($this->getMessage(), $logContext);
        }
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
