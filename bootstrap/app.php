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
                $message = $e->getMessage() ?: 'Kaynak bulunamadı';

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'RESOURCE_NOT_FOUND',
                    'reference' => ResponseReference::build($message, 404),
                ], 404);
            }
        });

        $exceptions->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kaynak bulunamadı',
                    'error_code' => 'RESOURCE_NOT_FOUND',
                    'reference' => ResponseReference::build('Kaynak bulunamadı', 404),
                ], 404);
            }
        });

        $exceptions->renderable(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Çok fazla istek gönderildi',
                    'error_code' => 'TOO_MANY_REQUESTS',
                    'reference' => ResponseReference::build('Çok fazla istek gönderildi', 429),
                ], 429);
            }
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kimlik doğrulama gerekli',
                    'error_code' => 'UNAUTHORIZED',
                    'reference' => ResponseReference::build('Kimlik doğrulama gerekli', 401),
                ], 401);
            }
        });

        $exceptions->renderable(function (HttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $message = $e->getMessage() ?: 'Sunucu hatası';

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'SERVER_ERROR',
                    'reference' => ResponseReference::build($message, $e->getStatusCode()),
                ], $e->getStatusCode());
            }
        });

        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $message = config('app.debug') ? $e->getMessage() : 'Sunucu hatası';

                $debugTrace = config('app.debug') ? [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ] : [];

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'INTERNAL_SERVER_ERROR',
                    'reference' => ResponseReference::build($message, 500, $debugTrace),
                ], 500);
            }
        });
    })->create();
