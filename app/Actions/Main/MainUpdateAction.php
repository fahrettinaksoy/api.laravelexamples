<?php

declare(strict_types=1);

namespace App\Actions\Main;

use App\Actions\BaseAction;

class MainUpdateAction extends BaseAction
{
    public function execute(int $id, array $data): mixed
    {
        return $this->repository->update($id, $data);
    }
}
