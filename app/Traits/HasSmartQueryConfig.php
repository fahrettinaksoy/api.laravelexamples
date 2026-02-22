<?php

declare(strict_types=1);

namespace App\Traits;

trait HasSmartQueryConfig
{
    public function getAllowedFiltering(): array
    {
        return $this->allowedFiltering;
    }

    public function getAllowedSorting(): array
    {
        return $this->allowedSorting;
    }

    public function getAllowedShowing(): array
    {
        return $this->allowedShowing;
    }

    public function getAllowedRelations(): array
    {
        return $this->allowedRelations;
    }

    public function getDefaultRelations(): array
    {
        return $this->defaultRelations;
    }

    public function getDefaultSorting(): string
    {
        return $this->defaultSorting;
    }
}
