<?php

declare(strict_types=1);

namespace App\Actions\Catalog\Product;

use App\Actions\BaseAction;

class ProductShowAction extends BaseAction
{
    public function execute(int $id): mixed
    {
        return $this->repository->findById($id);
    }
}
