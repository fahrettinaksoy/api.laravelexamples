<?php

use App\Http\Controllers\Catalog\Product\ProductController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PivotController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/catalog')->group(function () {
    Route::get('product', [ProductController::class, 'index']);
    Route::post('product', [ProductController::class, 'store']);
    Route::get('product/{id}', [ProductController::class, 'show'])->whereNumber('id');
    Route::put('product/{id}', [ProductController::class, 'update'])->whereNumber('id');
    Route::patch('product/{id}', [ProductController::class, 'patch'])->whereNumber('id');
    Route::delete('product/{id}', [ProductController::class, 'destroy'])->whereNumber('id');
});

Route::prefix('v1')->middleware('validate.module')->group(function () {
    Route::get('{path}/{id}', [PivotController::class, 'show'])->where('path', '.+/\d+/[a-z_]+')->whereNumber('id');
    Route::put('{path}/{id}', [PivotController::class, 'update'])->where('path', '.+/\d+/[a-z_]+')->whereNumber('id');
    Route::patch('{path}/{id}', [PivotController::class, 'patch'])->where('path', '.+/\d+/[a-z_]+')->whereNumber('id');
    Route::delete('{path}/{id}', [PivotController::class, 'destroy'])->where('path', '.+/\d+/[a-z_]+')->whereNumber('id');
    Route::get('{path}', [PivotController::class, 'index'])->where('path', '.+/\d+/[a-z_]+');
    Route::post('{path}', [PivotController::class, 'store'])->where('path', '.+/\d+/[a-z_]+');

    Route::get('{path}/{id}', [MainController::class, 'show'])->where('id', '[0-9]+')->where('path', '.*');
    Route::put('{path}/{id}', [MainController::class, 'update'])->where('id', '[0-9]+')->where('path', '.*');
    Route::patch('{path}/{id}', [MainController::class, 'patch'])->where('id', '[0-9]+')->where('path', '.*');
    Route::delete('{path}/{id}', [MainController::class, 'destroy'])->where('id', '[0-9]+')->where('path', '.*');
    Route::get('{path}', [MainController::class, 'index'])->where('path', '.*');
    Route::post('{path}', [MainController::class, 'store'])->where('path', '.*');
});
