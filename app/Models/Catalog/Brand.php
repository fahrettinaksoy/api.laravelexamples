<?php

declare(strict_types=1);

namespace App\Models\Catalog;

use App\Models\BaseModel;

class Brand extends BaseModel
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
