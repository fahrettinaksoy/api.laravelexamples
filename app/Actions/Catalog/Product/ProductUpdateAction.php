<?php

declare(strict_types=1);

namespace App\Actions\Catalog\Product;

use App\Actions\BaseAction;
use App\DataTransferObjects\Catalog\Product\ProductUpdateDTO;

class ProductUpdateAction extends BaseAction
{
    public function execute(int $id, ProductUpdateDTO $dto): mixed
    {
        return $this->repository->update($id, $dto->toArray());
    }
}
