<?php

declare(strict_types=1);

namespace App\SmartQuery\Concerns;

use App\SmartQuery\Builders\Sorts\AllowedSort;
use App\SmartQuery\Builders\Sorts\DefaultSort;
use App\SmartQuery\Exceptions\InvalidSortQuery;
use App\SmartQuery\SmartQueryRequest;

/**
 * SortsQuery Trait
 *
 * Adds sorting capabilities to SmartQuery
 */
trait SortsQuery
{
    protected array $allowedSorts = [];

    protected array $defaultSorts = [];

    /**
     * Set allowed sorts
     *
     * @param  array|string  $sorts
     * @return $this
     */
    public function allowedSorts($sorts): static
    {
        $sorts = is_array($sorts) ? $sorts : func_get_args();

        $normalized = [];

        foreach ($sorts as $sort) {
            if (is_string($sort)) {
                $sort = new AllowedSort($sort, new DefaultSort);
            } elseif (! $sort instanceof AllowedSort) {
                throw new \InvalidArgumentException(
                    'Sort must be a string or AllowedSort instance',
                );
            }

            $normalized[$sort->getName()] = $sort;
        }

        $this->allowedSorts = $normalized;

        $this->applySorts();

        return $this;
    }

    /**
     * Set default sort
     *
     * @param  string|array|AllowedSort  $sort
     * @return $this
     */
    public function defaultSort($sort): static
    {
        if ($sort instanceof AllowedSort) {
            $this->defaultSorts = [$sort];
        } elseif (is_array($sort)) {
            $this->defaultSorts = $sort;
        } else {
            $this->defaultSorts = [$sort];
        }

        return $this;
    }

    /**
     * Apply sorts from request
     */
    protected function applySorts(): void
    {
        $requestedSorts = $this->getRequestedSorts();

        if (empty($requestedSorts)) {
            $this->applyDefaultSorts();

            return;
        }

        foreach ($requestedSorts as $sort) {
            $this->applySort($sort);
        }
    }

    /**
     * Get requested sorts from request
     */
    protected function getRequestedSorts(): array
    {
        $sortParam = $this->request->input('sort', '');

        if (empty($sortParam)) {
            return [];
        }

        $delimiter = SmartQueryRequest::getSortsArrayValueDelimiter();

        if (is_string($sortParam)) {
            return explode($delimiter, $sortParam);
        }

        return (array) $sortParam;
    }

    /**
     * Apply a single sort
     */
    protected function applySort(string $sort): void
    {
        // Check for descending prefix (-)
        $descending = str_starts_with($sort, '-');
        $sortName = $descending ? substr($sort, 1) : $sort;

        $allowedSort = $this->findAllowedSort($sortName);

        if (! $allowedSort) {
            if (config('smartquery.throw_on_invalid_sort', true)) {
                throw InvalidSortQuery::sortNotAllowed(
                    $sortName,
                    $this->getAllowedSortNames(),
                );
            }

            return;
        }

        // Use default direction if set and no prefix
        if (! $descending && $allowedSort->hasDefaultDirection()) {
            $descending = $allowedSort->getDefaultDirection()->isDescending();
        }

        $sortClass = $allowedSort->getSortClass();
        $property = $allowedSort->getInternalName();

        $sortClass($this->builder, $descending, $property);
    }

    /**
     * Apply default sorts
     */
    protected function applyDefaultSorts(): void
    {
        foreach ($this->defaultSorts as $sort) {
            if ($sort instanceof AllowedSort) {
                $descending = $sort->hasDefaultDirection()
                    ? $sort->getDefaultDirection()->isDescending()
                    : false;

                $sortClass = $sort->getSortClass();
                $property = $sort->getInternalName();

                $sortClass($this->builder, $descending, $property);
            } elseif (is_string($sort)) {
                $this->applySort($sort);
            }
        }
    }

    protected function findAllowedSort(string $name): ?AllowedSort
    {
        return $this->allowedSorts[$name] ?? null;
    }

    protected function getAllowedSortNames(): array
    {
        return array_keys($this->allowedSorts);
    }
}
