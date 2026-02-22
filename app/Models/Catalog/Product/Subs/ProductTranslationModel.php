<?php

declare(strict_types=1);

namespace App\Models\Catalog\Product\Subs;

use App\DataTransferObjects\Catalog\Product\Subs\ProductTranslationDTO;
use App\Models\BaseModel;
use App\Models\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTranslationModel extends BaseModel
{
    protected $table = 'cat_product_translation';

    protected $primaryKey = 'product_translation_id';

    protected static ?string $fieldSource = ProductTranslationDTO::class;

    protected array $allowedRelations = [
        'product',
        'createdBy',
        'updatedBy',
    ];

    protected string $defaultSorting = 'locale';

    protected $casts = [
        'locale' => 'string',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}
