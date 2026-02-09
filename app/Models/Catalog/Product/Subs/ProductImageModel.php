<?php

declare(strict_types=1);

namespace App\Models\Catalog\Product\Subs;

use App\Models\BaseModel;
use App\Models\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImageModel extends BaseModel
{
    protected $table = 'cat_product_image';

    protected $primaryKey = 'product_image_id';

    public $fillable = [
        'product_id',
        'image_path',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    public array $allowedFiltering = [
        'product_id',
        'is_primary',
        'sort_order',
    ];

    public array $allowedSorting = [
        'sort_order',
        'created_at',
    ];

    public array $allowedRelations = [
        'product',
        'createdBy',
        'updatedBy',
    ];

    public string $defaultSorting = 'sort_order';

    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class);
    }
}
