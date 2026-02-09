<?php

declare(strict_types=1);

namespace Database\Factories\Catalog\Product\Subs;

use App\Models\Catalog\Product\Subs\ProductImageModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageModelFactory extends Factory
{
    protected $model = ProductImageModel::class;

    public function definition(): array
    {
        return [
            'image_path' => fake()->imageUrl(800, 600, 'products', true),
            'alt_text' => fake()->sentence(6),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_primary' => false,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'sort_order' => 0,
        ]);
    }
}
