<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * JSON:API yapisindaki type, id ve links alanlarini olusturur.
 * Resource tipi, benzersiz kimlik ve self link URL'i uretir.
 *
 * @mixin \Illuminate\Http\Resources\Json\JsonResource
 */
trait HasResourceLinks
{
    /**
     * Resource tipini dondurur (tablo adi).
     */
    protected function resolveResourceType(): string
    {
        return $this->resource->getTable();
    }

    /**
     * Resource'un benzersiz kimligini string olarak dondurur.
     */
    protected function resolveResourceId(): string
    {
        return (string) $this->resource->getKey();
    }

    /**
     * Resource icin self link URL'i olusturur.
     * Eger mevcut URL zaten ID ile bitiyorsa URL'i oldugu gibi dondurur.
     */
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
