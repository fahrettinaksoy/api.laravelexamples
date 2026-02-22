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

        if (auth()->check() && empty($model->created_by)) {
            $model->created_by = auth()->id();
        }

        if (auth()->check() && empty($model->updated_by)) {
            $model->updated_by = auth()->id();
        }
    }

    public function updating(BaseModel $model): void
    {
        if (auth()->check()) {
            $model->updated_by = auth()->id();
        }
    }
}
