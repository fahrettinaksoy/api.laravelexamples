<?php

declare(strict_types=1);

namespace App\Actions\Catalog\Product;

use App\Actions\BaseAction;

class ProductIndexAction extends BaseAction
{
    public function execute(array $filters = []): mixed
    {
        return $this->repository->paginate($filters);
    }
}
