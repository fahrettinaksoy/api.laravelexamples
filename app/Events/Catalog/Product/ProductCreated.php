<?php

declare(strict_types=1);

namespace App\Events\Catalog\Product;

use App\Models\Catalog\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Product $product
    ) {}
}
