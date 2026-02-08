<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Controllers\CommonController;
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
            ProductRepositoryCache::class
        );

        $this->app->when(CommonController::class)
            ->needs(BaseService::class)
            ->give(function ($app) {
                $modelClass = request()->attributes->get('modelClass');

                if (! $modelClass) {
                    throw new \RuntimeException('ValidateModule middleware required for CommonController');
                }

                $model = $app->make($modelClass);
                $repository = new BaseRepository($model);
                $cachedRepository = new BaseRepositoryCache($repository);

                return BaseService::make($cachedRepository);
            });
    }

    public function boot(): void {}
}
