<?php

declare(strict_types=1);

namespace App\Services\Catalog\Product;

use App\Actions\Catalog\Product\ProductDestroyAction;
use App\Actions\Catalog\Product\ProductIndexAction;
use App\Actions\Catalog\Product\ProductShowAction;
use App\Actions\Catalog\Product\ProductStoreAction;
use App\Actions\Catalog\Product\ProductUpdateAction;
use App\Events\Catalog\Product\ProductCreated;
use App\Events\Catalog\Product\ProductDeleted;
use App\Events\Catalog\Product\ProductUpdated;
use App\Exceptions\Catalog\Product\ProductNotFoundException;
use App\Repositories\Catalog\Product\ProductRepositoryInterface;
use App\Services\BaseService;

class ProductService extends BaseService
{
    public function __construct(
        ProductRepositoryInterface $repository,
    ) {
        parent::__construct($repository);

        $this->actions = [
            'filter' => new ProductIndexAction($repository),
            'show' => new ProductShowAction($repository),
            'store' => new ProductStoreAction($repository),
            'update' => new ProductUpdateAction($repository),
            'destroy' => new ProductDestroyAction($repository),
        ];
    }

    protected function dispatchCreatedEvent($item): void
    {
        event(new ProductCreated($item));
    }

    protected function dispatchUpdatedEvent($item): void
    {
        event(new ProductUpdated($item));
    }

    protected function dispatchDeletedEvent($item): void
    {
        event(new ProductDeleted($item));
    }

    protected function getNotFoundException(int $id): \Exception
    {
        return new ProductNotFoundException("Product with ID {$id} not found");
    }
}
