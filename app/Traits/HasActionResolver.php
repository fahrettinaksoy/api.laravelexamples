<?php

declare(strict_types=1);

namespace App\Traits;

use App\Http\Requests\BaseRequest;
use App\Services\BaseService;
use App\Support\DTOFactory;

trait HasActionResolver
{
    protected static array $validRequestActions = ['index', 'show', 'store', 'update', 'fieldUpdate', 'destroy'];

    protected static array $validDtoActions = ['store', 'update'];

    protected function validateActionKeys(): void
    {
        $invalidRequestKeys = array_diff(array_keys($this->requests), static::$validRequestActions);

        if (! empty($invalidRequestKeys)) {
            throw new \RuntimeException(
                'Invalid request action keys: ' . implode(', ', $invalidRequestKeys)
                . '. Valid keys: ' . implode(', ', static::$validRequestActions),
            );
        }

        $invalidDtoKeys = array_diff(array_keys($this->dtos), static::$validDtoActions);

        if (! empty($invalidDtoKeys)) {
            throw new \RuntimeException(
                'Invalid DTO action keys: ' . implode(', ', $invalidDtoKeys)
                . '. Valid keys: ' . implode(', ', static::$validDtoActions),
            );
        }
    }

    protected function getService(): BaseService
    {
        $this->service ??= app('dynamic.service');

        if ($this->service === null) {
            throw new \RuntimeException(
                'Service not initialized. Inject service via constructor or configure RepositoryServiceProvider.',
            );
        }

        return $this->service;
    }

    protected function resolveRequest(string $action): BaseRequest
    {
        if (! isset($this->requests[$action])) {
            throw new \RuntimeException("Request class not defined for action: {$action}");
        }

        $requestClass = $this->requests[$action];

        if (! is_subclass_of($requestClass, BaseRequest::class)) {
            throw new \RuntimeException("Request class '{$requestClass}' must extend BaseRequest");
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
