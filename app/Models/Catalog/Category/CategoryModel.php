<?php

declare(strict_types=1);

namespace App\Models\Catalog\Category;

use App\Models\BaseModel;
use App\Models\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryModel extends BaseModel
{
    protected $table = 'cat_category';

    public $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(ProductModel::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CategoryModel::class, 'parent_id');
    }
}
