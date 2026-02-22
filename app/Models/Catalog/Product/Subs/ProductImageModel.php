<?php

declare(strict_types=1);

namespace App\Models\Catalog\Product\Subs;

use App\DataTransferObjects\Catalog\Product\Subs\ProductImageDTO;
use App\Models\BaseModel;
use App\Models\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImageModel extends BaseModel
{
    protected $table = 'cat_product_image';

    protected $primaryKey = 'product_image_id';

    protected static ?string $fieldSource = ProductImageDTO::class;

    protected array $allowedRelations = [
        'product',
        'createdBy',
        'updatedBy',
    ];

    protected string $defaultSorting = 'sort_order';

    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}
