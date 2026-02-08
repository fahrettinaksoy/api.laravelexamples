<?php

declare(strict_types=1);

namespace App\Events\Catalog\Product;

use App\Models\Catalog\Product\ProductModel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ProductModel $product
    ) {}
}
