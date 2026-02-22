<?php

declare(strict_types=1);

namespace App\Http\Resources\Catalog\Product;

use App\Http\Resources\BaseCollection;

class ProductCollection extends BaseCollection
{
    public $collects = ProductResource::class;
}
