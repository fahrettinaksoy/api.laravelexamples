<?php

declare(strict_types=1);

namespace App\Traits;

use App\Http\Requests\BaseRequest;
use App\Services\BaseService;
use App\Support\DTOFactory;
use App\Support\DynamicServiceFactory;

trait HasActionResolver
{
    protected static array $validRequestActions = ['index', 'show', 'store', 'update', 'fieldUpdate', 'destroy'];

    protected static array $validDtoActions = ['store', 'update'];

    protected function validateActionKeys(): void
    {
        $invalidRequestKeys = array_diff(array_keys($this->requests), static::$validRequestActions);

        if (! empty($invalidRequestKeys)) {
            throw new \RuntimeException(
                __('api.controller.invalid_request_keys', [
                    'keys' => implode(', ', $invalidRequestKeys),
                    'valid' => implode(', ', static::$validRequestActions),
                ]),
            );
        }

        $invalidDtoKeys = array_diff(array_keys($this->dtos), static::$validDtoActions);

        if (! empty($invalidDtoKeys)) {
            throw new \RuntimeException(
                __('api.controller.invalid_dto_keys', [
                    'keys' => implode(', ', $invalidDtoKeys),
                    'valid' => implode(', ', static::$validDtoActions),
                ]),
            );
        }
    }

    protected function getService(): BaseService
    {
        if ($this->service !== null) {
            return $this->service;
        }

        $modelClass = request()->attributes->get('modelClass');

        if (! $modelClass) {
            throw new \RuntimeException(
                __('api.controller.service_not_initialized'),
            );
        }

        $this->service = app(DynamicServiceFactory::class)->make($modelClass);

        return $this->service;
    }

    protected function resolveRequest(string $action): BaseRequest
    {
        if (! isset($this->requests[$action])) {
            throw new \RuntimeException(
                __('api.controller.request_not_defined', ['action' => $action]),
            );
        }

        $requestClass = $this->requests[$action];

        if (! is_subclass_of($requestClass, BaseRequest::class)) {
            throw new \RuntimeException(
                __('api.controller.request_must_extend_base', ['class' => $requestClass]),
            );
        }

        return app($requestClass);
    }

    protected function createDTO(BaseRequest $request, string $type): array
    {
        if (isset($this->dtos[$type])) {
            return DTOFactory::fromRequest($this->dtos[$type], $request, $type)->only();
        }

        return $request->validated();
    }
}
