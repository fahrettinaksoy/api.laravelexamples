<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\BaseModel;
use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryCache;
use App\Services\BaseService;

class DynamicServiceFactory
{
    public function make(string $modelClass): BaseService
    {
        if (! is_subclass_of($modelClass, BaseModel::class)) {
            throw new \InvalidArgumentException(
                __('api.service.invalid_model_class', ['class' => $modelClass]),
            );
        }

        $model = app($modelClass);
        $repository = new BaseRepository($model);
        $cachedRepository = new BaseRepositoryCache($repository, $model->getTable());

        return new BaseService($cachedRepository);
    }
}
