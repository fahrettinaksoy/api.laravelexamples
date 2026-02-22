<?php

declare(strict_types=1);

namespace App\Services\Catalog\Product;

use App\Actions\Catalog\Product\ProductDestroyAction;
use App\Actions\Catalog\Product\ProductIndexAction;
use App\Actions\Catalog\Product\ProductShowAction;
use App\Actions\Catalog\Product\ProductStoreAction;
use App\Actions\Catalog\Product\ProductUpdateAction;
use App\Repositories\Catalog\Product\ProductRepositoryInterface;
use App\Services\BaseService;

class ProductService extends BaseService
{
    public function __construct(
        ProductRepositoryInterface $repository,
    ) {
        parent::__construct($repository, [
            'index' => new ProductIndexAction($repository),
            'show' => new ProductShowAction($repository),
            'store' => new ProductStoreAction($repository),
            'update' => new ProductUpdateAction($repository),
            'destroy' => new ProductDestroyAction($repository),
        ]);
    }
}
