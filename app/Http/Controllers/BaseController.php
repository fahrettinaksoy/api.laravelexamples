<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BaseService;
use App\Traits\HasActionResolver;
use App\Traits\HasQueryContext;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{
    use AuthorizesRequests;
    use HasActionResolver;
    use HasQueryContext;
    use ValidatesRequests;

    public function __construct(
        protected ?BaseService $service = null,
        protected string $resourceClass = '',
        protected string $collectionClass = '',
        protected array $requests = [],
        protected array $dtos = [],
    ) {
        $this->validateActionKeys();
    }

    public function index(): JsonResponse
    {
        $request = $this->resolveRequest('index');

        $data = $this->getService()->index($this->buildQueryContext($request));

        return (new $this->collectionClass($data))
            ->withMessage(__('api.success'))
            ->response();
    }

    public function show(int $id): JsonResponse
    {
        $request = $this->resolveRequest('show');

        $data = $this->getService()->show($id, $this->parseIncludes($request));

        return (new $this->resourceClass($data))
            ->withMessage(__('api.success'))
            ->response();
    }

    public function store(): JsonResponse
    {
        $request = $this->resolveRequest('store');

        $data = $this->createDTO($request, 'store');
        $result = $this->getService()->store($data);

        return (new $this->resourceClass($result))
            ->withMessage(__('api.created'))
            ->withStatusCode(201)
            ->response();
    }

    public function update(int $id): JsonResponse
    {
        $request = $this->resolveRequest('update');

        $data = $this->createDTO($request, 'update');
        $result = $this->getService()->update($id, $data);

        return (new $this->resourceClass($result))
            ->withMessage(__('api.updated'))
            ->response();
    }

    public function patch(int $id): JsonResponse
    {
        $request = $this->resolveRequest('fieldUpdate');

        $field = $request->validated('field');
        $value = $request->validated('value');

        $result = $this->getService()->update($id, [$field => $value]);

        return (new $this->resourceClass($result))
            ->withMessage(__('api.updated'))
            ->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->resolveRequest('destroy');

        $this->getService()->destroy($id);

        return (new $this->resourceClass(null))
            ->withMessage(__('api.deleted'))
            ->response();
    }
}
