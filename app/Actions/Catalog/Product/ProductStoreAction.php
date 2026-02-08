<?php

declare(strict_types=1);

namespace App\Actions\Catalog\Product;

use App\Actions\BaseAction;
use App\DataTransferObjects\Catalog\Product\ProductStoreDTO;
use App\Models\Catalog\Product\ProductModel;

class ProductStoreAction extends BaseAction
{
    public function execute(ProductStoreDTO $dto): ProductModel
    {
        return $this->repository->create($dto->toArray());
    }
}
