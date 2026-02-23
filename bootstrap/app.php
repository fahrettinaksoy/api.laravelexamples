<?php

use App\Support\ResponseReference;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \App\Http\Middleware\AssignRequestId::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
        ]);

        $middleware->alias([
            'validate.module' => \App\Http\Middleware\ValidateModule::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $message = $e->getMessage() ?: __('api.not_found');

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'RESOURCE_NOT_FOUND',
                    'reference' => app(ResponseReference::class)->build($message, 404),
                ], 404);
            }
        });

        $exceptions->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                $message = __('api.not_found');

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'RESOURCE_NOT_FOUND',
                    'reference' => app(ResponseReference::class)->build($message, 404),
                ], 404);
            }
        });

        $exceptions->renderable(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $message = __('api.too_many_requests');

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'TOO_MANY_REQUESTS',
                    'reference' => app(ResponseReference::class)->build($message, 429),
                ], 429);
            }
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                $message = __('api.unauthorized');

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'UNAUTHORIZED',
                    'reference' => app(ResponseReference::class)->build($message, 401),
                ], 401);
            }
        });

        $exceptions->renderable(function (HttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $message = $e->getMessage() ?: __('api.internal_error');

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'SERVER_ERROR',
                    'reference' => app(ResponseReference::class)->build($message, $e->getStatusCode()),
                ], $e->getStatusCode());
            }
        });

        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $message = config('app.debug') ? $e->getMessage() : __('api.internal_error');

                $debugTrace = config('app.debug') ? [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ] : [];

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'INTERNAL_SERVER_ERROR',
                    'reference' => app(ResponseReference::class)->build($message, 500, $debugTrace),
                ], 500);
            }
        });
    })->create();
