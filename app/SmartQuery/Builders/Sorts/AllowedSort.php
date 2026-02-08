<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Sorts;

use App\SmartQuery\Enums\SortDirection;
use Closure;

class AllowedSort
{
    protected string $name;

    protected ?string $internalName;

    protected Sort $sortClass;

    protected ?SortDirection $defaultDirection = null;

    public function __construct(
        string $name,
        Sort $sortClass,
        ?string $internalName = null
    ) {
        $this->name = $name;
        $this->sortClass = $sortClass;
        $this->internalName = $internalName ?? $name;
    }

    public static function field(string $name, string $column): static
    {
        return new static(
            $name,
            new DefaultSort,
            $column
        );
    }

    public static function custom(string $name, Sort $sort, ?string $column = null): static
    {
        return new static(
            $name,
            new CustomSort($sort),
            $column
        );
    }

    public static function callback(string $name, Closure $callback, ?string $column = null): static
    {
        return new static(
            $name,
            new CallbackSort($callback),
            $column
        );
    }

    public function defaultDirection(SortDirection $direction): static
    {
        $this->defaultDirection = $direction;

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

    public function getSortClass(): Sort
    {
        return $this->sortClass;
    }

    public function getDefaultDirection(): ?SortDirection
    {
        return $this->defaultDirection;
    }

    public function hasDefaultDirection(): bool
    {
        return $this->defaultDirection !== null;
    }
}
