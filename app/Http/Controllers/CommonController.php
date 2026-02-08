<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BaseDestroyRequest;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\BaseShowRequest;
use App\Http\Requests\BaseStoreRequest;
use App\Http\Requests\BaseUpdateRequest;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Responses\ApiResponse;

class CommonController extends BaseController
{
    public function __construct(ApiResponse $apiResponse)
    {
        parent::__construct(
            apiResponse: $apiResponse,
            service: null,
            resourceClass: BaseResource::class,
            collectionClass: BaseCollection::class,
            requests: [
                'index' => BaseIndexRequest::class,
                'show' => BaseShowRequest::class,
                'store' => BaseStoreRequest::class,
                'update' => BaseUpdateRequest::class,
                'destroy' => BaseDestroyRequest::class,
            ]
        );
    }
}
