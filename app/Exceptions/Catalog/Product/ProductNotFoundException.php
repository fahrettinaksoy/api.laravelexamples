<?php

declare(strict_types=1);

namespace App\Exceptions\Catalog\Product;

use App\Exceptions\BaseException;

class ProductNotFoundException extends BaseException
{
    protected int $statusCode = 404;

    protected string $errorCode = 'PRODUCT_NOT_FOUND';
}
