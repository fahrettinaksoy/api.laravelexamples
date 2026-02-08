<?php

declare(strict_types=1);

namespace App\Models\Catalog\Brand;

use App\Models\BaseModel;
use App\Models\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrandModel extends BaseModel
{
    protected $table = 'cat_brand';

    public $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(ProductModel::class);
    }
}
