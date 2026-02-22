<?php

declare(strict_types=1);

namespace App\Actions\Main;

use App\Actions\BaseAction;

class MainDestroyAction extends BaseAction
{
    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function executeWithFilter(array $criteria): int
    {
        return $this->repository->deleteMany($criteria);
    }
}
