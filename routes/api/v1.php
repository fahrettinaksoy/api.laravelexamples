<?php

use App\Http\Controllers\Catalog\Product\ProductController;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

// Product - Custom Controller (specific routes)
Route::prefix('v1/catalog')->group(function () {
    Route::get('product', [ProductController::class, 'index']);
    Route::post('product', [ProductController::class, 'store']);
    Route::get('product/{id}', [ProductController::class, 'show']);
    Route::put('product/{id}', [ProductController::class, 'update']);
    Route::delete('product/{id}', [ProductController::class, 'destroy']);
});

// Dynamic Routes - CommonController (automatic module/resource resolution)
// Dynamic Routes - CommonController (automatic module/resource resolution)
Route::prefix('v1')->middleware('validate.module')->group(function () {
    // 1. Item Routes (Ends with ID)
    Route::match(['get', 'put', 'delete'], '{path}/{id}', function (Illuminate\Http\Request $request, string $path, string $id) {
        $method = strtolower($request->method());
        $action = match ($method) {
            'get' => 'show',
            'put' => 'update',
            'delete' => 'destroy',
        };
        return app(CommonController::class)->callAction($action, ['id' => $id]);
    })->where('id', '[0-9a-f\-]+')->where('path', '.*');

    // 2. Collection Routes (No ID at end) - MUST be defined after Item Routes to avoid conflict if logic was different, but here regex handles it.
    Route::match(['get', 'post'], '{path}', function (Illuminate\Http\Request $request, string $path) {
        $method = strtolower($request->method());
        $action = match ($method) {
            'get' => 'index',
            'post' => 'store',
        };
        return app(CommonController::class)->callAction($action, []);
    })->where('path', '.*');
});
