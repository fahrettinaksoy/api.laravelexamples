<?php

declare(strict_types=1);

namespace App\Http\Controllers\Catalog\Product;

use App\DataTransferObjects\Catalog\Product\ProductStoreDTO;
use App\DataTransferObjects\Catalog\Product\ProductUpdateDTO;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Catalog\Product\ProductDestroyRequest;
use App\Http\Requests\Catalog\Product\ProductIndexRequest;
use App\Http\Requests\Catalog\Product\ProductShowRequest;
use App\Http\Requests\Catalog\Product\ProductStoreRequest;
use App\Http\Requests\Catalog\Product\ProductUpdateRequest;
use App\Http\Resources\Catalog\Product\ProductCollection;
use App\Http\Resources\Catalog\Product\ProductResource;
use App\Http\Responses\ApiResponse;
use App\Services\Catalog\Product\ProductService;

class ProductController extends BaseController
{
    public function __construct(
        ApiResponse $apiResponse,
        ProductService $service
    ) {
        parent::__construct(
            apiResponse: $apiResponse,
            service: $service,
            resourceClass: ProductResource::class,
            collectionClass: ProductCollection::class,
            requests: [
                'index' => ProductIndexRequest::class,
                'show' => ProductShowRequest::class,
                'store' => ProductStoreRequest::class,
                'update' => ProductUpdateRequest::class,
                'destroy' => ProductDestroyRequest::class,
            ],
            dtos: [
                'store' => ProductStoreDTO::class,
                'update' => ProductUpdateDTO::class,
            ]
        );
    }
}
