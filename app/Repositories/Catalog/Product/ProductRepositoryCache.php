<?php

declare(strict_types=1);

namespace App\Repositories\Catalog\Product;

use App\Models\Catalog\Product\ProductModel;
use App\Repositories\BaseRepositoryCache;

class ProductRepositoryCache extends BaseRepositoryCache implements ProductRepositoryInterface
{
    public function __construct(ProductRepository $repository, ProductModel $model)
    {
        parent::__construct($repository, $model->getTable());
    }
}
