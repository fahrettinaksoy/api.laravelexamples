<?php

declare(strict_types=1);

namespace App\Repositories\Catalog\Product;

use App\Models\Catalog\Product;
use App\Repositories\BaseRepository;

/**
 * Product Repository
 */
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function findBySku(string $sku): ?Product
    {
        return $this->model->where('sku', $sku)->first();
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getInStock(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->inStock()->get();
    }

    public function getOnSale(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->onSale()->get();
    }

    public function getFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->featured()->get();
    }

    public function getByCategory(string $categoryId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('category_id', $categoryId)->get();
    }

    public function getByBrand(string $brandId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('brand_id', $brandId)->get();
    }
}
