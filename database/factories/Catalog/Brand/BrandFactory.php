<?php

declare(strict_types=1);

namespace Database\Factories\Catalog\Brand;

use App\Models\Catalog\Brand\BrandModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalog\Brand\BrandModel>
 */
class BrandFactory extends Factory
{
    protected $model = BrandModel::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'logo' => fake()->imageUrl(200, 200, 'business', true, $name),
            'is_active' => fake()->boolean(90), // 90% active
        ];
    }

    /**
     * Indicate that the brand is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the brand is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
