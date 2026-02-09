<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User\UserModel;
use App\Traits\LocaleHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

abstract class BaseModel extends Model
{
    use HasFactory;
    use LocaleHelper;
    use SoftDeletes;

    protected $connection = 'conn_mysql';

    public $fillable = [];

    public $keyType = 'int';

    public array $allowedFiltering = [];

    public array $allowedSorting = [];

    public array $allowedShowing = [];

    public array $allowedRelations = [];

    public array $defaultRelations = [];

    public string $defaultSorting = '-id';

    public $incrementing = true;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }

            if (auth()->check() && empty($model->created_by)) {
                $model->created_by = auth()->id();
            }

            if (auth()->check() && empty($model->updated_by)) {
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(UserModel::class, 'id', 'created_by');
    }

    public function updatedBy(): HasOne
    {
        return $this->hasOne(UserModel::class, 'id', 'updated_by');
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? $this->formatDateTimeByCurrentLocale($value) : null,
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? $this->formatDateTimeByCurrentLocale($value) : null,
        );
    }

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }
}
