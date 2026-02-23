<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

class ResponseReference
{
    public function __construct(
        private readonly Request $request,
    ) {}

    public function build(string $message, int $statusCode = 200, array $debugTrace = []): array
    {
        $reference = [
            'message' => $message,
            'status_code' => $statusCode,
            'timestamp' => now()->toISOString(),
            'locale' => app()->getLocale(),
            'version' => $this->resolveVersion(),
            'request_id' => $this->resolveRequestId(),
            'response_time' => $this->resolveResponseTime(),
        ];

        if (config('app.debug')) {
            $reference['environment'] = config('app.env');
            $reference['cache_driver'] = config('cache.default');
            $reference['debug'] = true;

            if (! empty($debugTrace)) {
                $reference['debug_trace'] = $debugTrace;
            }
        }

        return $reference;
    }

    private function resolveVersion(): string
    {
        $path = $this->request->path();

        if (preg_match('/api\/(v\d+)/', $path, $matches)) {
            return $matches[1];
        }

        return 'v1';
    }

    private function resolveRequestId(): string
    {
        return (string) ($this->request->attributes->get('request_id', ''));
    }

    private function resolveResponseTime(): string
    {
        if (defined('LARAVEL_START')) {
            $ms = round((microtime(true) - LARAVEL_START) * 1000);

            return $ms . 'ms';
        }

        return '0ms';
    }
}
