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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

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
    public function index(): JsonResponse
    {
        $this->injectParentScope();

        return parent::index();
    }

    public function store(): JsonResponse
    {
        $this->injectParentScope();

        return parent::store();
    }

    protected function injectParentScope(): void
    {
        if (request()->attributes->get('isPivotRoute')) {
            $parentId = request()->attributes->get('parentId');
            $parentModelClass = request()->attributes->get('parentModelClass');

            if ($parentId && $parentModelClass) {
                // Determine foreign key: ProductModel -> product -> product_id
                $parentName = Str::snake(
                    Str::before(class_basename($parentModelClass), 'Model')
                );
                $foreignKey = $parentName.'_id';

                // Merge into request for filtering (index) and saving (store)
                request()->merge([$foreignKey => $parentId]);
            }
        }
    }
}
