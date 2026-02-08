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
Route::prefix('v1')->middleware('validate.module')->group(function () {
    Route::get('{module}/{resource}', [CommonController::class, 'index']);
    Route::post('{module}/{resource}', [CommonController::class, 'store']);
    Route::get('{module}/{resource}/{id}', [CommonController::class, 'show']);
    Route::put('{module}/{resource}/{id}', [CommonController::class, 'update']);
    Route::delete('{module}/{resource}/{id}', [CommonController::class, 'destroy']);
});
