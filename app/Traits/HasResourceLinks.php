<?php

declare(strict_types=1);

namespace App\Traits;

trait HasResourceLinks
{
    protected function resolveResourceType(): string
    {
        return $this->resource->getTable();
    }

    protected function resolveResourceId(): string
    {
        return (string) $this->resource->getKey();
    }

    protected function resolveSelfLink(): string
    {
        $baseUrl = request()->url();
        $id = $this->resolveResourceId();

        if (str_ends_with($baseUrl, '/' . $id)) {
            return $baseUrl;
        }

        return rtrim($baseUrl, '/') . '/' . $id;
    }
}
