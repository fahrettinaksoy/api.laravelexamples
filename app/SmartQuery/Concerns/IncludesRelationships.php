<?php

declare(strict_types=1);

namespace App\SmartQuery\Concerns;

use App\SmartQuery\Builders\Includes\AllowedInclude;
use App\SmartQuery\Builders\Includes\RawIncludeHandler;
use App\SmartQuery\Builders\Includes\RelationshipInclude;
use App\SmartQuery\Exceptions\InvalidIncludeQuery;
use App\SmartQuery\SmartQueryRequest;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait IncludesRelationships
{
    protected array $allowedIncludes = [];

    public function allowedIncludes($includes): static
    {
        $includes = is_array($includes) ? $includes : func_get_args();

        $normalized = [];

        foreach ($includes as $include) {
            if (is_string($include)) {
                $include = AllowedInclude::relationship($include);
            } elseif (! $include instanceof AllowedInclude) {
                throw new \InvalidArgumentException(
                    __('api.smartquery.invalid_include_type', [
                        'type' => get_debug_type($include),
                    ]),
                );
            }

            $normalized[$include->getName()] = $include;
        }

        $this->allowedIncludes = $normalized;

        $this->autoAddCountAndExistsIncludes();

        $this->applyIncludes();

        return $this;
    }

    protected function autoAddCountAndExistsIncludes(): void
    {
        foreach ($this->allowedIncludes as $include) {
            $includeClass = get_class($include->getIncludeClass());

            if ($includeClass === RelationshipInclude::class) {
                $name = $include->getName();

                $countInclude = AllowedInclude::count($name . 'Count', $name);
                $existsInclude = AllowedInclude::exists($name . 'Exists', $name);

                $this->allowedIncludes[$countInclude->getName()] = $countInclude;
                $this->allowedIncludes[$existsInclude->getName()] = $existsInclude;
            }
        }
    }

    protected function applyIncludes(): void
    {
        $requestedIncludes = $this->getRequestedIncludes();

        if (empty($requestedIncludes)) {
            return;
        }

        foreach ($requestedIncludes as $include) {
            $this->applyInclude($include);
        }
    }

    protected function getRequestedIncludes(): array
    {
        $includeParam = $this->request->input('include', '');

        if (empty($includeParam)) {
            return [];
        }

        $delimiter = SmartQueryRequest::getIncludesArrayValueDelimiter();

        if (is_string($includeParam)) {
            return explode($delimiter, $includeParam);
        }

        return (array) $includeParam;
    }

    protected function applyInclude(string $includeName): void
    {
        $allowedInclude = $this->findAllowedInclude($includeName);

        if (! $allowedInclude) {
            if (config('smartquery.throw_on_invalid_include', true)) {
                throw InvalidIncludeQuery::includeNotAllowed(
                    $includeName,
                    $this->getAllowedIncludeNames(),
                );
            }

            return;
        }

        $includeClass = $allowedInclude->getIncludeClass();
        $internalName = $allowedInclude->getInternalName();

        if ($this->useEloquent && $this->builder instanceof EloquentBuilder) {
            $includeClass($this->builder, $internalName);
        } elseif (! $this->useEloquent && isset($this->model)) {
            $handler = new RawIncludeHandler;
            $handler->apply($this->builder, $includeName, $this->model);
        }
    }

    protected function findAllowedInclude(string $name): ?AllowedInclude
    {
        return $this->allowedIncludes[$name] ?? null;
    }

    protected function getAllowedIncludeNames(): array
    {
        return array_keys($this->allowedIncludes);
    }
}
