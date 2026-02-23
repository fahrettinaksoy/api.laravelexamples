<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\ResponseReference;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
        ];

        $allHealthy = ! in_array('fail', array_column($checks, 'status'), true);
        $statusCode = $allHealthy ? 200 : 503;

        return response()->json([
            'success' => $allHealthy,
            'data' => [
                'status' => $allHealthy ? 'healthy' : 'unhealthy',
                'checks' => $checks,
            ],
            'reference' => app(ResponseReference::class)->build(
                $allHealthy ? __('api.health.healthy') : __('api.health.unhealthy'),
                $statusCode,
            ),
        ], $statusCode);
    }

    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::connection('conn_mysql')->getPdo();
            $latency = round((microtime(true) - $start) * 1000);

            return [
                'status' => 'pass',
                'latency_ms' => $latency,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'fail',
                'message' => config('app.debug') ? $e->getMessage() : __('api.health.connection_failed'),
            ];
        }
    }

    private function checkCache(): array
    {
        try {
            $start = microtime(true);
            $key = 'health_check_' . time();
            Cache::put($key, true, 5);
            $result = Cache::get($key);
            Cache::forget($key);
            $latency = round((microtime(true) - $start) * 1000);

            return [
                'status' => $result === true ? 'pass' : 'fail',
                'driver' => config('cache.default'),
                'latency_ms' => $latency,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'fail',
                'driver' => config('cache.default'),
                'message' => config('app.debug') ? $e->getMessage() : __('api.health.connection_failed'),
            ];
        }
    }
}
