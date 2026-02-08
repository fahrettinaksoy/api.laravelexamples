<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
                'index' => \App\Http\Requests\BaseIndexRequest::class,
                'show' => \App\Http\Requests\BaseShowRequest::class,
                'store' => \App\Http\Requests\BaseStoreRequest::class,
                'update' => \App\Http\Requests\BaseUpdateRequest::class,
                'destroy' => \App\Http\Requests\BaseDestroyRequest::class,
            ]
        );
    }
}
