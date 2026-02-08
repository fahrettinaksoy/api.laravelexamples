<?php

declare(strict_types=1);

namespace App\Repositories\Catalog\Product;

use App\Models\Catalog\Product;
use App\Repositories\BaseRepositoryInterface;

/**
 * Product Repository Interface
 */
interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find product by SKU
     */
    public function findBySku(string $sku): ?Product;

    /**
     * Find product by slug
     */
    public function findBySlug(string $slug): ?Product;

    /**
     * Get products in stock
     */
    public function getInStock(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get products on sale
     */
    public function getOnSale(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get featured products
     */
    public function getFeatured(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get products by category
     */
    public function getByCategory(string $categoryId): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get products by brand
     */
    public function getByBrand(string $brandId): \Illuminate\Database\Eloquent\Collection;
}
