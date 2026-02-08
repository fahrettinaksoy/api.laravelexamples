<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\Formatters\CollectionResponse;
use App\Http\Responses\Formatters\ErrorResponse;
use App\Http\Responses\Formatters\PaginatedResponse;
use App\Http\Responses\Formatters\ResourceResponse;
use App\Http\Responses\Formatters\SuccessResponse;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SuccessResponse::class);
        $this->app->singleton(ErrorResponse::class);
        $this->app->singleton(ResourceResponse::class);
        $this->app->singleton(CollectionResponse::class);
        $this->app->singleton(PaginatedResponse::class);
        $this->app->singleton(ApiResponse::class, function ($app) {
            return new ApiResponse(
                $app->make(SuccessResponse::class),
                $app->make(ErrorResponse::class),
                $app->make(ResourceResponse::class),
                $app->make(CollectionResponse::class),
                $app->make(PaginatedResponse::class),
            );
        });
    }

    public function boot(): void {}
}
