<?php

declare(strict_types=1);

namespace App\Actions\Main;

use App\Actions\BaseAction;
use Illuminate\Database\Eloquent\Model;

class MainUpdateAction extends BaseAction
{
    public function execute(int $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }
}
