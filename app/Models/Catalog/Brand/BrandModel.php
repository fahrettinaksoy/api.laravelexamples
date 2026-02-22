<?php

declare(strict_types=1);

namespace App\Models\Catalog\Brand;

use App\DataTransferObjects\Catalog\Brand\BrandDTO;
use App\Models\BaseModel;
use App\Models\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrandModel extends BaseModel
{
    protected $table = 'cat_brand';

    protected $primaryKey = 'brand_id';

    protected static ?string $fieldSource = BrandDTO::class;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(ProductModel::class, 'brand_id');
    }
}
