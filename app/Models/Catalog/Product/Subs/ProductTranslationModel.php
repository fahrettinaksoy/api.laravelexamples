<?php

declare(strict_types=1);

namespace App\Models\Catalog\Product\Subs;

use App\Models\BaseModel;
use App\Models\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTranslationModel extends BaseModel
{
    protected $table = 'cat_product_translation';

    protected $primaryKey = 'product_translation_id';

    public $fillable = [
        'product_id',
        'locale',
        'name',
        'description',
        'short_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public array $allowedFiltering = [
        'product_id',
        'locale',
        'name',
    ];

    public array $allowedSorting = [
        'locale',
        'name',
        'created_at',
    ];

    public array $allowedRelations = [
        'product',
        'createdBy',
        'updatedBy',
    ];

    public string $defaultSorting = 'locale';

    protected $casts = [
        'locale' => 'string',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class);
    }

    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }
}
