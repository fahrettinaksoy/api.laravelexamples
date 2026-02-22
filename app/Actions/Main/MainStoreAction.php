<?php

declare(strict_types=1);

namespace App\Actions\Main;

use App\Actions\BaseAction;

class MainStoreAction extends BaseAction
{
    public function execute(array $data): mixed
    {
        return $this->repository->create($data);
    }
}
