<?php

declare(strict_types=1);

namespace App\Actions\Main;

use App\Actions\BaseAction;
use Illuminate\Database\Eloquent\Model;

class MainStoreAction extends BaseAction
{
    public function execute(array $data): Model
    {
        return $this->repository->create($data);
    }
}
