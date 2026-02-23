<?php

declare(strict_types=1);

namespace App\Models\Catalog\Category;

use App\DataTransferObjects\Catalog\Category\CategoryDTO;
use App\Models\BaseModel;
use App\Models\Catalog\Product\ProductModel;
use App\SmartQuery\Builders\Filters\AllowedFilter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryModel extends BaseModel
{
    protected $table = 'cat_category';

    protected $primaryKey = 'category_id';

    protected static ?string $fieldSource = CategoryDTO::class;

    protected array $allowedRelations = ['products', 'parent', 'children', 'createdBy', 'updatedBy'];

    protected string $defaultSorting = '-created_at';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getAllowedFilters(): array
    {
        return [
            'name',
            'slug',
            AllowedFilter::exact('parent_id'),
            AllowedFilter::exact('is_active'),
            AllowedFilter::trashed(),
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(ProductModel::class, 'category_id');
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
