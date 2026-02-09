<?php

declare(strict_types=1);

namespace App\Actions\Catalog\Product;

use App\Actions\BaseAction;

class ProductDestroyAction extends BaseAction
{
    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
