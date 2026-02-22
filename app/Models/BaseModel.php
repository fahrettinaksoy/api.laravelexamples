<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User\UserModel;
use App\Observers\BaseModelObserver;
use App\Traits\HasFieldMetadata;
use App\Traits\HasLocaleDateFormat;
use App\Traits\HasSmartQueryConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use HasFactory;
    use HasFieldMetadata;
    use HasLocaleDateFormat;
    use HasSmartQueryConfig;
    use SoftDeletes;

    protected $connection = 'conn_mysql';

    protected static ?string $fieldSource = null;

    protected $fillable = [];

    protected array $allowedFiltering = [];

    protected array $allowedSorting = [];

    protected array $allowedShowing = [];

    protected array $allowedRelations = [];

    protected array $defaultRelations = [];

    protected string $defaultSorting = '-created_at';

    public $keyType = 'int';

    public $incrementing = true;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected static function booted(): void
    {
        static::observe(BaseModelObserver::class);
    }

    public function getRouteKeyName(): string
    {
        return $this->getKeyName();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'updated_by');
    }
}
