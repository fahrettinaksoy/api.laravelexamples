<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    protected ApiResponse $apiResponse;

    public function __construct(
        ApiResponse $apiResponse,
        protected mixed $service = null,
        protected string $resourceClass = '',
        protected string $collectionClass = '',
        protected array $requests = [],
        protected array $dtos = []
    ) {
        $this->apiResponse = $apiResponse;
    }

    protected function getService(): mixed
    {
        if ($this->service === null) {
            $modelClass = request()->attributes->get('modelClass');

            if (! $modelClass) {
                throw new \RuntimeException('Service not initialized. Either inject service in constructor or use ValidateModule middleware.');
            }

            $model = app($modelClass);
            $repository = new \App\Repositories\BaseRepository($model);
            $cachedRepository = new \App\Repositories\BaseRepositoryCache($repository);
            $this->service = \App\Services\BaseService::make($cachedRepository);
        }

        return $this->service;
    }

    public function index(): JsonResponse
    {
        app($this->requests['index']);

        $data = $this->getService()->filter(request()->all());

        return $this->apiResponse->paginated(
            $data,
            $this->collectionClass,
            'Records retrieved successfully'
        );
    }

    public function show(string $id): JsonResponse
    {
        app($this->requests['show']);

        $data = $this->getService()->show([]);

        return $this->apiResponse->resource(
            $data,
            $this->resourceClass,
            'Record retrieved successfully'
        );
    }

    public function store(): JsonResponse
    {
        $request = app($this->requests['store']);

        $dto = $this->createDTO($request, 'store');
        $data = $this->getService()->store($dto->toArray());

        return $this->apiResponse->resource(
            $data,
            $this->resourceClass,
            'Record created successfully',
            201
        );
    }

    public function update(string $id): JsonResponse
    {
        $request = app($this->requests['update']);

        $dto = $this->createDTO($request, 'update');
        $data = $this->getService()->update($dto->toArray());

        return $this->apiResponse->resource(
            $data,
            $this->resourceClass,
            'Record updated successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        app($this->requests['destroy']);

        $this->getService()->destroy([]);

        return $this->apiResponse->success('Record deleted successfully');
    }

    protected function createDTO($request, string $type): mixed
    {
        if (isset($this->dtos[$type])) {
            $dtoClass = $this->dtos[$type];

            return $dtoClass::fromRequest($request);
        }

        return $request->validated();
    }
}
