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
    public function run(): void
    {
        $this->seedPages();
        $this->seedBrands();
        $this->seedCategories();
        $this->seedProducts();
    }

    private function seedPages(): void
    {
        PageModel::factory()->published()->count(10)->create();
        PageModel::factory()->draft()->count(5)->create();
    }

    private function seedBrands(): void
    {
        BrandModel::factory()->active()->count(10)->create();
    }

    private function seedCategories(): void
    {
        $rootCategories = CategoryModel::factory()->root()->count(5)->create();
        foreach ($rootCategories as $parent) {
            CategoryModel::factory()
                ->withParent($parent->category_id)
                ->count(3)
                ->create();
        }
    }

    private function seedProducts(): void
    {
        $brands = BrandModel::all();
        $categories = CategoryModel::whereNull('parent_id')->get();
        ProductModel::factory()
            ->count(30)
            ->recycle($brands)
            ->recycle($categories)
            ->create()
            ->each(function ($product) use ($brands, $categories) {
                $product->brand_id = $brands->random()->brand_id;
                $product->category_id = $categories->random()->category_id;
                $product->save();

                ProductImageModel::factory()
                    ->primary()
                    ->create(['product_id' => $product->product_id]);

                ProductImageModel::factory()
                    ->count(rand(2, 4))
                    ->create(['product_id' => $product->product_id]);

                ProductTranslationModel::factory()
                    ->turkish()
                    ->create(['product_id' => $product->product_id]);

                ProductTranslationModel::factory()
                    ->english()
                    ->create(['product_id' => $product->product_id]);
            });

        ProductModel::factory()
            ->featured()
            ->inStock()
            ->count(5)
            ->recycle($brands)
            ->recycle($categories)
            ->create()
            ->each(function ($product) use ($brands, $categories) {
                $product->brand_id = $brands->random()->brand_id;
                $product->category_id = $categories->random()->category_id;
                $product->save();

                ProductImageModel::factory()
                    ->primary()
                    ->create(['product_id' => $product->product_id]);

                ProductImageModel::factory()
                    ->count(rand(3, 5))
                    ->create(['product_id' => $product->product_id]);

                ProductTranslationModel::factory()
                    ->turkish()
                    ->create(['product_id' => $product->product_id]);

                ProductTranslationModel::factory()
                    ->english()
                    ->create(['product_id' => $product->product_id]);
            });

        ProductModel::factory()
            ->onSale()
            ->inStock()
            ->count(10)
            ->recycle($brands)
            ->recycle($categories)
            ->create()
            ->each(function ($product) use ($brands, $categories) {
                $product->brand_id = $brands->random()->brand_id;
                $product->category_id = $categories->random()->category_id;
                $product->save();

                ProductImageModel::factory()
                    ->primary()
                    ->create(['product_id' => $product->product_id]);

                ProductImageModel::factory()
                    ->count(rand(2, 3))
                    ->create(['product_id' => $product->product_id]);

                ProductTranslationModel::factory()
                    ->turkish()
                    ->create(['product_id' => $product->product_id]);

                ProductTranslationModel::factory()
                    ->english()
                    ->create(['product_id' => $product->product_id]);
            });
    }
}
