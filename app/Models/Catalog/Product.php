<?php

declare(strict_types=1);

namespace App\Models\Catalog;

use App\Models\BaseModel;
use App\SmartQuery\Builders\Filters\AllowedFilter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends BaseModel
{
    protected $table = 'cat_product';

    public $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'sale_price',
        'cost',
        'stock',
        'category_id',
        'brand_id',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public array $allowedFiltering = [
        'name',
        'slug',
        'sku',
        'description',
        'category_id',
        'brand_id',
        'is_active',
        'is_featured',
    ];

    public function getAllowedFilters(): array
    {
        return [
            'name',
            'slug',
            'sku',
            'description',
            AllowedFilter::exact('category_id'),
            AllowedFilter::exact('brand_id'),
            AllowedFilter::exact('is_active'),
            AllowedFilter::exact('is_featured'),
            AllowedFilter::scope('inStock'),
            AllowedFilter::scope('onSale'),
            AllowedFilter::scope('featured'),
            AllowedFilter::trashed(),
        ];
    }

    public array $allowedSorting = [
        'name',
        'price',
        'sale_price',
        'stock',
        'created_at',
        'updated_at',
    ];

    public array $allowedRelations = [
        'category',
        'brand',
        'createdBy',
        'updatedBy',
    ];

    public string $defaultSorting = '-created_at';

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
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')
            ->where('sale_price', '<', $this->getConnection()->raw('price'));
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where('is_active', true);
    }
}
