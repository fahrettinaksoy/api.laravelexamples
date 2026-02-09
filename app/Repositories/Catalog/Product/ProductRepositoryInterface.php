<?php

declare(strict_types=1);

namespace App\Repositories\Catalog\Product;

use App\Models\Catalog\Product\ProductModel;
use App\Repositories\BaseRepositoryInterface;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySku(string $sku): ?ProductModel;

    public function findBySlug(string $slug): ?ProductModel;

    public function getInStock(): \Illuminate\Database\Eloquent\Collection;

    public function getOnSale(): \Illuminate\Database\Eloquent\Collection;

    public function getFeatured(): \Illuminate\Database\Eloquent\Collection;

    public function getByCategory(int $categoryId): \Illuminate\Database\Eloquent\Collection;

    public function getByBrand(int $brandId): \Illuminate\Database\Eloquent\Collection;
}
