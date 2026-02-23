<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Catalog\Product\ProductRepositoryCache;
use App\Repositories\Catalog\Product\ProductRepositoryInterface;
use App\Support\DynamicServiceFactory;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepositoryCache::class,
        );

        $this->app->singleton(DynamicServiceFactory::class);
    }

    public function boot(): void {}
}
