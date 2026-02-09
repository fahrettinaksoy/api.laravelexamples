<?php

declare(strict_types=1);

namespace App\Actions\Main;

use App\Actions\BaseAction;

class MainActionShow extends BaseAction
{
    public function execute(int $id): mixed
    {
        return $this->repository->findById($id);
    }
}
