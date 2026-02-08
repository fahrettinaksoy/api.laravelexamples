<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Catalog\Brand;
use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use App\Models\Content\Page;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Content Module
        $this->seedPages();

        // Catalog Module
        $this->seedBrands();
        $this->seedCategories();
        $this->seedProducts();
    }

    /**
     * Seed pages
     */
    private function seedPages(): void
    {
        Page::factory()->published()->count(10)->create();
        Page::factory()->draft()->count(5)->create();
    }

    /**
     * Seed brands
     */
    private function seedBrands(): void
    {
        Brand::factory()->active()->count(10)->create();
    }

    /**
     * Seed categories with hierarchy
     */
    private function seedCategories(): void
    {
        // Root categories
        $rootCategories = Category::factory()->root()->count(5)->create();

        // Child categories
        foreach ($rootCategories as $parent) {
            Category::factory()
                ->withParent($parent->id)
                ->count(3)
                ->create();
        }
    }

    /**
     * Seed products
     */
    private function seedProducts(): void
    {
        $brands = Brand::all();
        $categories = Category::whereNull('parent_id')->get();

        // Regular products
        Product::factory()
            ->count(30)
            ->recycle($brands)
            ->recycle($categories)
            ->create()
            ->each(function ($product) use ($brands, $categories) {
                $product->brand_id = $brands->random()->id;
                $product->category_id = $categories->random()->id;
                $product->save();
            });

        // Featured products
        Product::factory()
            ->featured()
            ->inStock()
            ->count(5)
            ->recycle($brands)
            ->recycle($categories)
            ->create()
            ->each(function ($product) use ($brands, $categories) {
                $product->brand_id = $brands->random()->id;
                $product->category_id = $categories->random()->id;
                $product->save();
            });

        // Sale products
        Product::factory()
            ->onSale()
            ->inStock()
            ->count(10)
            ->recycle($brands)
            ->recycle($categories)
            ->create()
            ->each(function ($product) use ($brands, $categories) {
                $product->brand_id = $brands->random()->id;
                $product->category_id = $categories->random()->id;
                $product->save();
            });
    }
}
