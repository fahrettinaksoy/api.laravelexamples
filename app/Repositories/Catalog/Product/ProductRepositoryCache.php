<?php

declare(strict_types=1);

namespace App\Repositories\Catalog\Product;

use App\Models\Catalog\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

/**
 * Product Repository Cache Decorator
 */
class ProductRepositoryCache implements ProductRepositoryInterface
{
    private const CACHE_TTL = 3600;

    private const CACHE_TAG = 'products';

    public function __construct(
        private readonly ProductRepository $repository
    ) {}

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $cacheKey = 'products.paginate.'.md5(serialize($filters));

        return Cache::tags([self::CACHE_TAG])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => $this->repository->paginate($filters)
        );
    }

    public function findById(string $id): ?Product
    {
        return Cache::tags([self::CACHE_TAG])->remember(
            "product.{$id}",
            self::CACHE_TTL,
            fn () => $this->repository->findById($id)
        );
    }

    public function findBySku(string $sku): ?Product
    {
        return Cache::tags([self::CACHE_TAG])->remember(
            "product.sku.{$sku}",
            self::CACHE_TTL,
            fn () => $this->repository->findBySku($sku)
        );
    }

    public function findBySlug(string $slug): ?Product
    {
        return Cache::tags([self::CACHE_TAG])->remember(
            "product.slug.{$slug}",
            self::CACHE_TTL,
            fn () => $this->repository->findBySlug($slug)
        );
    }

    public function all(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->all($filters);
    }

    public function create(array $data): Product
    {
        $result = $this->repository->create($data);
        $this->clearCache();

        return $result;
    }

    public function update(string $id, array $data): Product
    {
        $result = $this->repository->update($id, $data);
        $this->clearCache($id);

        return $result;
    }

    public function delete(string $id): bool
    {
        $result = $this->repository->delete($id);
        $this->clearCache($id);

        return $result;
    }

    public function findBy(string $field, mixed $value): ?Product
    {
        return $this->repository->findBy($field, $value);
    }

    public function getBy(string $field, mixed $value): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getBy($field, $value);
    }

    public function getInStock(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::tags([self::CACHE_TAG])->remember(
            'products.inStock',
            self::CACHE_TTL,
            fn () => $this->repository->getInStock()
        );
    }

    public function getOnSale(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::tags([self::CACHE_TAG])->remember(
            'products.onSale',
            self::CACHE_TTL,
            fn () => $this->repository->getOnSale()
        );
    }

    public function getFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::tags([self::CACHE_TAG])->remember(
            'products.featured',
            self::CACHE_TTL,
            fn () => $this->repository->getFeatured()
        );
    }

    public function getByCategory(string $categoryId): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::tags([self::CACHE_TAG])->remember(
            "products.category.{$categoryId}",
            self::CACHE_TTL,
            fn () => $this->repository->getByCategory($categoryId)
        );
    }

    public function getByBrand(string $brandId): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::tags([self::CACHE_TAG])->remember(
            "products.brand.{$brandId}",
            self::CACHE_TTL,
            fn () => $this->repository->getByBrand($brandId)
        );
    }

    private function clearCache(?string $id = null): void
    {
        Cache::tags([self::CACHE_TAG])->flush();
    }

    public function getModel(): Product
    {
        return $this->repository->getModel();
    }
}
