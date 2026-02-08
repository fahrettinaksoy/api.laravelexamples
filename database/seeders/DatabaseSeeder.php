<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Catalog\Brand\BrandModel;
use App\Models\Catalog\Category\CategoryModel;
use App\Models\Catalog\Product\ProductModel;
use App\Models\Catalog\Product\Subs\ProductImageModel;
use App\Models\Catalog\Product\Subs\ProductTranslationModel;
use App\Models\Content\Page\PageModel;
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
        PageModel::factory()->published()->count(10)->create();
        PageModel::factory()->draft()->count(5)->create();
    }

    /**
     * Seed brands
     */
    private function seedBrands(): void
    {
        BrandModel::factory()->active()->count(10)->create();
    }

    /**
     * Seed categories with hierarchy
     */
    private function seedCategories(): void
    {
        // Root categories
        $rootCategories = CategoryModel::factory()->root()->count(5)->create();

        // Child categories
        foreach ($rootCategories as $parent) {
            CategoryModel::factory()
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
        $brands = BrandModel::all();
        $categories = CategoryModel::whereNull('parent_id')->get();

        // Regular products
        ProductModel::factory()
            ->count(30)
            ->recycle($brands)
            ->recycle($categories)
            ->create()
            ->each(function ($product) use ($brands, $categories) {
                $product->brand_id = $brands->random()->id;
                $product->category_id = $categories->random()->id;
                $product->save();

                // Add images
                ProductImageModel::factory()
                    ->primary()
                    ->create(['product_id' => $product->id]);

                ProductImageModel::factory()
                    ->count(rand(2, 4))
                    ->create(['product_id' => $product->id]);

                // Add translations
                ProductTranslationModel::factory()
                    ->turkish()
                    ->create(['product_id' => $product->id]);

                ProductTranslationModel::factory()
                    ->english()
                    ->create(['product_id' => $product->id]);
            });

        // Featured products
        ProductModel::factory()
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

                // Add images
                ProductImageModel::factory()
                    ->primary()
                    ->create(['product_id' => $product->id]);

                ProductImageModel::factory()
                    ->count(rand(3, 5))
                    ->create(['product_id' => $product->id]);

                // Add translations
                ProductTranslationModel::factory()
                    ->turkish()
                    ->create(['product_id' => $product->id]);

                ProductTranslationModel::factory()
                    ->english()
                    ->create(['product_id' => $product->id]);
            });

        // Sale products
        ProductModel::factory()
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

                // Add images
                ProductImageModel::factory()
                    ->primary()
                    ->create(['product_id' => $product->id]);

                ProductImageModel::factory()
                    ->count(rand(2, 3))
                    ->create(['product_id' => $product->id]);

                // Add translations
                ProductTranslationModel::factory()
                    ->turkish()
                    ->create(['product_id' => $product->id]);

                ProductTranslationModel::factory()
                    ->english()
                    ->create(['product_id' => $product->id]);
            });
    }
}
