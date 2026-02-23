<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(\App\Support\ResponseReference::class);

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureGates();
        $this->validateCorsConfiguration();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute((int) env('RATE_LIMIT_API', 60))
                ->by($request->user()?->id ?: $request->ip())
                ->response(fn () => $this->rateLimitResponse());
        });

        RateLimiter::for('api-public', function (Request $request) {
            return Limit::perMinute((int) env('RATE_LIMIT_API_PUBLIC', 30))
                ->by($request->ip())
                ->response(fn () => $this->rateLimitResponse());
        });

        RateLimiter::for('api-write', function (Request $request) {
            return Limit::perMinute((int) env('RATE_LIMIT_API_WRITE', 20))
                ->by($request->user()?->id ?: $request->ip())
                ->response(fn () => $this->rateLimitResponse());
        });

        RateLimiter::for('api-auth', function (Request $request) {
            return Limit::perMinute((int) env('RATE_LIMIT_API_AUTH', 10))
                ->by($request->ip())
                ->response(fn () => $this->rateLimitResponse());
        });
    }

    private function configureGates(): void
    {
        Gate::define('viewPulse', function ($user = null) {
            return $this->app->environment('local');
        });
    }

    private function validateCorsConfiguration(): void
    {
        if (! $this->app->environment('production')) {
            return;
        }

        $origins = config('cors.allowed_origins', []);

        if (in_array('*', $origins, true)) {
            Log::critical('CORS wildcard (*) is configured in production. Set CORS_ALLOWED_ORIGINS to specific domains.');
        }

        if (empty($origins)) {
            Log::warning('CORS allowed_origins is empty. No cross-origin requests will be accepted. Set CORS_ALLOWED_ORIGINS env variable.');
        }
    }

    private function rateLimitResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => __('api.too_many_requests'),
            'error_code' => 'TOO_MANY_REQUESTS',
            'reference' => app(\App\Support\ResponseReference::class)->build(__('api.too_many_requests'), 429),
        ], 429);
    }
}
