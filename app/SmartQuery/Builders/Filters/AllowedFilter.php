<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Filters;

use App\SmartQuery\Enums\FilterOperator;
use Closure;

class AllowedFilter
{
    protected string $name;

    protected ?string $internalName;

    protected Filter $filterClass;

    protected mixed $defaultValue = null;

    protected bool $hasDefault = false;

    protected bool $isNullable = false;

    protected array $ignoredValues = [];

    protected ?string $arrayValueDelimiter = null;

    public function __construct(
        string $name,
        Filter $filterClass,
        ?string $internalName = null,
    ) {
        $this->name = $name;
        $this->filterClass = $filterClass;
        $this->internalName = $internalName ?? $name;
    }

    public static function exact(
        string $name,
        ?string $column = null,
        bool $addRelationConstraint = true,
    ): static {
        return new static(
            $name,
            new ExactFilter($addRelationConstraint),
            $column,
        );
    }

    public static function partial(
        string $name,
        ?string $column = null,
        bool $addRelationConstraint = true,
    ): static {
        return new static(
            $name,
            new PartialFilter($addRelationConstraint),
            $column,
        );
    }

    public static function beginsWithStrict(string $name, ?string $column = null): static
    {
        return new static(
            $name,
            new BeginsWithStrictFilter,
            $column,
        );
    }

    public static function endsWithStrict(string $name, ?string $column = null): static
    {
        return new static(
            $name,
            new EndsWithStrictFilter,
            $column,
        );
    }

    public static function scope(string $name, ?string $scope = null): static
    {
        return new static(
            $name,
            new ScopeFilter($scope),
            $scope,
        );
    }

    public static function callback(string $name, Closure $callback): static
    {
        return new static(
            $name,
            new CallbackFilter($callback),
        );
    }

    public static function custom(string $name, Filter $filter, ?string $column = null): static
    {
        return new static(
            $name,
            new CustomFilter($filter),
            $column,
        );
    }

    public static function belongsTo(string $name, ?string $relationship = null): static
    {
        return new static(
            $name,
            new BelongsToFilter($relationship),
            $relationship,
        );
    }

    public static function trashed(): static
    {
        return new static(
            'trashed',
            new TrashedFilter,
        );
    }

    public static function operator(
        string $name,
        FilterOperator $operator = FilterOperator::EQUAL,
        ?string $column = null,
    ): static {
        return new static(
            $name,
            new OperatorFilter($operator),
            $column,
        );
    }

    public function default($value): static
    {
        $this->defaultValue = $value;
        $this->hasDefault = true;

        return $this;
    }

    public function nullable(): static
    {
        $this->isNullable = true;

        return $this;
    }

    public function ignore(...$values): static
    {
        $this->ignoredValues = $values;

        return $this;
    }

    public function delimiter(string $delimiter): static
    {
        $this->arrayValueDelimiter = $delimiter;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInternalName(): string
    {
        return $this->internalName;
    }

    public function getFilterClass(): Filter
    {
        return $this->filterClass;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function hasDefaultValue(): bool
    {
        return $this->hasDefault;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function getIgnoredValues(): array
    {
        return $this->ignoredValues;
    }

    public function getArrayValueDelimiter(): ?string
    {
        return $this->arrayValueDelimiter;
    }
}
