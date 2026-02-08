<?php

declare(strict_types=1);

namespace App\Actions\Main;

use App\Actions\BaseAction;

class MainActionFilter extends BaseAction
{
    public function execute(array $filters = []): mixed
    {
        return $this->repository->paginate($filters);
    }
}
