<?php

declare(strict_types=1);

namespace Database\Factories\Catalog\Brand;

use App\Models\Catalog\Brand\BrandModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BrandModelFactory extends Factory
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
            'is_active' => fake()->boolean(90),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
