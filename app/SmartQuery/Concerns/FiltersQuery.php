<?php

declare(strict_types=1);

namespace App\SmartQuery\Concerns;

use App\SmartQuery\Builders\Filters\AllowedFilter;
use App\SmartQuery\Exceptions\InvalidFilterQuery;
use App\SmartQuery\SmartQueryRequest;

trait FiltersQuery
{
    protected array $allowedFilters = [];

    protected function normalizeFilters(array $filters): array
    {
        return collect($filters)->map(function ($filter) {
            if (is_string($filter)) {
                return AllowedFilter::partial($filter);
            }

            if ($filter instanceof AllowedFilter) {
                return $filter;
            }

            throw new \InvalidArgumentException(
                'Filter must be string or AllowedFilter instance. ' .
                'Got: ' . get_debug_type($filter),
            );
        })->toArray();
    }

    public function allowedFilters($filters): static
    {
        if (! empty($filters)) {
            $filters = is_array($filters) ? $filters : func_get_args();
            $this->allowedFilters = $this->normalizeFilters($filters);
            $this->applyFilters();

            return $this;
        }

        if (method_exists($this->getModel(), 'getAllowedFilters')) {
            $modelFilters = $this->getModel()->getAllowedFilters();
            $this->allowedFilters = $this->normalizeFilters($modelFilters);
            $this->applyFilters();

            return $this;
        }

        if (property_exists($this->getModel(), 'allowedFiltering')) {
            $modelFilters = $this->getModel()->allowedFiltering;
            $this->allowedFilters = $this->normalizeFilters($modelFilters);
            $this->applyFilters();

            return $this;
        }

        $this->applyFilters();

        return $this;
    }

    protected function applyFilters(): void
    {
        $filters = $this->request->input('filter', []);

        if (empty($filters) || ! is_array($filters)) {
            $this->applyDefaultFilters();

            return;
        }

        foreach ($filters as $filterName => $value) {
            $this->applyFilter($filterName, $value);
        }

        $this->applyDefaultFilters();
    }

    protected function applyFilter(string $filterName, $value): void
    {
        $allowedFilter = $this->findAllowedFilter($filterName);

        if (! $allowedFilter) {
            if (config('smartquery.throw_on_invalid_filter', true)) {
                throw InvalidFilterQuery::filterNotAllowed(
                    $filterName,
                    $this->getAllowedFilterNames(),
                );
            }

            return;
        }

        if ($this->isIgnoredValue($allowedFilter, $value)) {
            return;
        }

        if ($allowedFilter->isNullable() && ($value === '' || $value === null)) {
            $property = $allowedFilter->getInternalName();
            $this->builder->whereNull($property);

            return;
        }

        $value = $this->parseArrayValue($allowedFilter, $value);
        $filter = $allowedFilter->getFilterClass();
        $property = $allowedFilter->getInternalName();

        $filter($this->builder, $value, $property);
    }

    protected function applyDefaultFilters(): void
    {
        $requestFilters = $this->request->input('filter', []);

        foreach ($this->allowedFilters as $allowedFilter) {
            if (! $allowedFilter->hasDefaultValue()) {
                continue;
            }

            if (isset($requestFilters[$allowedFilter->getName()])) {
                continue;
            }

            $filter = $allowedFilter->getFilterClass();
            $property = $allowedFilter->getInternalName();
            $value = $allowedFilter->getDefaultValue();

            $filter($this->builder, $value, $property);
        }
    }

    protected function findAllowedFilter(string $name): ?AllowedFilter
    {
        foreach ($this->allowedFilters as $filter) {
            if ($filter->getName() === $name) {
                return $filter;
            }
        }

        return null;
    }

    protected function isIgnoredValue(AllowedFilter $filter, $value): bool
    {
        return in_array($value, $filter->getIgnoredValues(), true);
    }

    protected function parseArrayValue(AllowedFilter $filter, $value)
    {
        if (! is_string($value)) {
            return $value;
        }

        $delimiter = $filter->getArrayValueDelimiter()
            ?? SmartQueryRequest::getFilterArrayValueDelimiter();

        if (str_contains($value, $delimiter)) {
            return explode($delimiter, $value);
        }

        return $value;
    }

    protected function getAllowedFilterNames(): array
    {
        return array_map(
            fn (AllowedFilter $filter) => $filter->getName(),
            $this->allowedFilters,
        );
    }
}
