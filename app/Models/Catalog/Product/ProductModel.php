<?php

declare(strict_types=1);

namespace App\Models\Catalog\Product;

use App\DataTransferObjects\Catalog\Product\ProductDTO;
use App\Models\BaseModel;
use App\Models\Catalog\Brand\BrandModel;
use App\Models\Catalog\Category\CategoryModel;
use App\Models\Catalog\Product\Subs\ProductImageModel;
use App\Models\Catalog\Product\Subs\ProductTranslationModel;
use App\SmartQuery\Builders\Filters\AllowedFilter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductModel extends BaseModel
{
    protected $table = 'cat_product';

    protected $primaryKey = 'product_id';

    protected static ?string $fieldSource = ProductDTO::class;

    public function getAllowedFilters(): array
    {
        return [
            'name',
            'slug',
            'sku',
            'stock',
            'description',
            AllowedFilter::exact('category_id'),
            AllowedFilter::exact('brand_id'),
            AllowedFilter::exact('is_active'),
            AllowedFilter::exact('is_featured'),
            AllowedFilter::trashed(),
        ];
    }

    protected array $allowedRelations = [
        'category',
        'brand',
        'images',
        'translations',
        'primaryImage',
        'createdBy',
        'updatedBy',
    ];

    protected string $defaultSorting = '-created_at';

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(BrandModel::class, 'brand_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImageModel::class, 'product_id', 'product_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslationModel::class, 'product_id', 'product_id');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImageModel::class, 'product_id')->where('is_primary', true);
    }
}
