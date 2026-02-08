<?php

declare(strict_types=1);

namespace Database\Factories\Catalog;

use App\Models\Catalog\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalog\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        $price = fake()->randomFloat(2, 10, 1000);
        $hasSale = fake()->boolean(30); // 30% on sale

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'sku' => strtoupper(fake()->bothify('??###??')),
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(20),
            'price' => $price,
            'sale_price' => $hasSale ? $price * 0.8 : null, // 20% discount
            'cost' => $price * 0.6, // 40% margin
            'stock' => fake()->numberBetween(0, 500),
            'category_id' => null, // Set via relationship
            'brand_id' => null, // Set via relationship
            'is_active' => fake()->boolean(85), // 85% active
            'is_featured' => fake()->boolean(20), // 20% featured
            'meta_title' => ucfirst($name).' - '.fake()->words(3, true),
            'meta_description' => fake()->sentence(15),
            'meta_keywords' => implode(', ', fake()->words(5)),
        ];
    }

    /**
     * Indicate that the product is on sale.
     */
    public function onSale(): static
    {
        return $this->state(function (array $attributes) {
            $price = $attributes['price'];

            return [
                'sale_price' => $price * fake()->randomFloat(2, 0.5, 0.9),
            ];
        });
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    /**
     * Indicate that the product is in stock.
     */
    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(50, 500),
        ]);
    }

    /**
     * Set category for the product.
     */
    public function forCategory(string $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }

    /**
     * Set brand for the product.
     */
    public function forBrand(string $brandId): static
    {
        return $this->state(fn (array $attributes) => [
            'brand_id' => $brandId,
        ]);
    }
}
