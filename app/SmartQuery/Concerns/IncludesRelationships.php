<?php

declare(strict_types=1);

namespace App\SmartQuery\Concerns;

use App\SmartQuery\Builders\Includes\AllowedInclude;
use App\SmartQuery\Builders\Includes\RawIncludeHandler;
use App\SmartQuery\Builders\Includes\RelationshipInclude;
use App\SmartQuery\Exceptions\InvalidIncludeQuery;
use App\SmartQuery\SmartQueryRequest;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * IncludesRelationships Trait
 *
 * Adds relationship include capabilities to SmartQuery
 */
trait IncludesRelationships
{
    protected array $allowedIncludes = [];

    /**
     * Set allowed includes
     *
     * @param  array|string  $includes
     * @return $this
     */
    public function allowedIncludes($includes): static
    {
        $includes = is_array($includes) ? $includes : func_get_args();

        $this->allowedIncludes = collect($includes)->map(function ($include) {
            // String include name - use relationship include by default
            if (is_string($include)) {
                return AllowedInclude::relationship($include);
            }

            // AllowedInclude instance
            if ($include instanceof AllowedInclude) {
                return $include;
            }

            throw new \InvalidArgumentException(
                'Include must be a string or AllowedInclude instance'
            );
        })->toArray();

        // Auto-add Count and Exists variants for relationship includes
        $this->autoAddCountAndExistsIncludes();

        $this->applyIncludes();

        return $this;
    }

    /**
     * Auto-add Count and Exists variants for relationship includes
     */
    protected function autoAddCountAndExistsIncludes(): void
    {
        $additionalIncludes = [];

        foreach ($this->allowedIncludes as $include) {
            $includeClass = get_class($include->getIncludeClass());

            // Only auto-add for RelationshipInclude
            if ($includeClass === RelationshipInclude::class) {
                $name = $include->getName();

                // Add Count variant
                $additionalIncludes[] = AllowedInclude::count($name.'Count', $name);

                // Add Exists variant
                $additionalIncludes[] = AllowedInclude::exists($name.'Exists', $name);
            }
        }

        $this->allowedIncludes = array_merge($this->allowedIncludes, $additionalIncludes);
    }

    /**
     * Apply includes from request
     */
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

    /**
     * Get requested includes from request
     */
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

    /**
     * Apply a single include
     */
    protected function applyInclude(string $includeName): void
    {
        $allowedInclude = $this->findAllowedInclude($includeName);

        if (! $allowedInclude) {
            if (config('smartquery.throw_on_invalid_include', true)) {
                throw InvalidIncludeQuery::includeNotAllowed(
                    $includeName,
                    $this->getAllowedIncludeNames()
                );
            }

            return;
        }

        $includeClass = $allowedInclude->getIncludeClass();
        $internalName = $allowedInclude->getInternalName();

        // Eloquent mode - use include class directly
        if ($this->useEloquent && $this->builder instanceof EloquentBuilder) {
            $includeClass($this->builder, $internalName);
        }
        // Raw mode - use RawIncludeHandler
        elseif (! $this->useEloquent && isset($this->model)) {
            $handler = new RawIncludeHandler;
            $handler->apply($this->builder, $includeName, $this->model);
        }
    }

    /**
     * Find allowed include by name
     */
    protected function findAllowedInclude(string $name): ?AllowedInclude
    {
        foreach ($this->allowedIncludes as $include) {
            if ($include->getName() === $name) {
                return $include;
            }
        }

        return null;
    }

    /**
     * Get all allowed include names
     */
    protected function getAllowedIncludeNames(): array
    {
        return array_map(
            fn (AllowedInclude $include) => $include->getName(),
            $this->allowedIncludes
        );
    }
}
