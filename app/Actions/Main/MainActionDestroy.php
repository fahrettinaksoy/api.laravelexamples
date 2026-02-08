<?php

declare(strict_types=1);

namespace App\Actions\Main;

use App\Actions\BaseAction;

class MainActionDestroy extends BaseAction
{
    public function execute(int $id): mixed
    {
        return $this->repository->delete((string) $id);
    }

    public function executeWithFilter(mixed $filter): mixed
    {
        return $this->repository->delete($filter);
    }
}
