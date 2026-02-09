<?php

declare(strict_types=1);

namespace Database\Factories\Catalog\Product;

use App\Models\Catalog\Product\ProductModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductModelFactory extends Factory
{
    protected $model = ProductModel::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        $price = fake()->randomFloat(2, 10, 1000);
        $hasSale = fake()->boolean(30);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'sku' => strtoupper(fake()->bothify('??###??')),
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(20),
            'price' => $price,
            'sale_price' => $hasSale ? $price * 0.8 : null,
            'cost' => $price * 0.6,
            'stock' => fake()->numberBetween(0, 500),
            'category_id' => null,
            'brand_id' => null,
            'is_active' => fake()->boolean(85),
            'is_featured' => fake()->boolean(20),
            'meta_title' => ucfirst($name).' - '.fake()->words(3, true),
            'meta_description' => fake()->sentence(15),
            'meta_keywords' => implode(', ', fake()->words(5)),
        ];
    }

    public function onSale(): static
    {
        return $this->state(function (array $attributes) {
            $price = $attributes['price'];

            return [
                'sale_price' => $price * fake()->randomFloat(2, 0.5, 0.9),
            ];
        });
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_active' => true,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(50, 500),
        ]);
    }

    public function forCategory(int $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }

    public function forBrand(int $brandId): static
    {
        return $this->state(fn (array $attributes) => [
            'brand_id' => $brandId,
        ]);
    }
}
