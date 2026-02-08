<?php

declare(strict_types=1);

namespace App\Exceptions\Catalog\Product;

use Exception;

class ProductNotFoundException extends Exception
{
    protected $code = 404;

    protected $message = 'Product not found';
}
