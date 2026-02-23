<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BaseModel;
use Illuminate\Support\Str;

class BaseModelObserver
{
    public function creating(BaseModel $model): void
    {
        if (empty($model->uuid)) {
            $model->uuid = (string) Str::uuid();
        }

        $userId = auth()->id() ? (int) auth()->id() : null;

        if ($userId !== null && empty($model->created_by)) {
            $model->created_by = $userId;
        }

        if ($userId !== null && empty($model->updated_by)) {
            $model->updated_by = $userId;
        }
    }

    public function updating(BaseModel $model): void
    {
        $userId = auth()->id() ? (int) auth()->id() : null;

        if ($userId !== null) {
            $model->updated_by = $userId;
        }
    }
}
