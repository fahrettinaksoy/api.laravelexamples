<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

class ResponseReference
{
    public static function build(string $message, int $statusCode = 200, array $debugTrace = []): array
    {
        $reference = [
            'message' => $message,
            'status_code' => $statusCode,
            'timestamp' => now()->toISOString(),
            'locale' => app()->getLocale(),
            'version' => self::resolveVersion(),
            'request_id' => self::resolveRequestId(),
            'response_time' => self::resolveResponseTime(),
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

    private static function resolveVersion(): string
    {
        $path = request()->path();

        if (preg_match('/api\/(v\d+)/', $path, $matches)) {
            return $matches[1];
        }

        return 'v1';
    }

    private static function resolveRequestId(): string
    {
        $request = request();

        if (! $request->attributes->has('request_id')) {
            $request->attributes->set('request_id', (string) Str::uuid());
        }

        return $request->attributes->get('request_id');
    }

    private static function resolveResponseTime(): string
    {
        if (defined('LARAVEL_START')) {
            $ms = round((microtime(true) - LARAVEL_START) * 1000);

            return $ms . 'ms';
        }

        return '0ms';
    }
}
