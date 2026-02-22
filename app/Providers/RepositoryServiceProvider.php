<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryCache;
use App\Repositories\Catalog\Product\ProductRepositoryCache;
use App\Repositories\Catalog\Product\ProductRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepositoryCache::class,
        );

        $this->app->bind('dynamic.service', function ($app) {
            $modelClass = request()->attributes->get('modelClass');

            if (! $modelClass) {
                return;
            }

            $model = $app->make($modelClass);
            $repository = new BaseRepository($model);
            $cachedRepository = new BaseRepositoryCache($repository, $model->getTable());

            return new BaseService($cachedRepository);
        });
    }

    public function boot(): void {}
}
