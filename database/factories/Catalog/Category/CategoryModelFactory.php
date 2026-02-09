<?php

declare(strict_types=1);

namespace Database\Factories\Catalog\Category;

use App\Models\Catalog\Category\CategoryModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryModelFactory extends Factory
{
    protected $model = CategoryModel::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'parent_id' => null,
            'is_active' => fake()->boolean(85),
        ];
    }

    public function withParent(?int $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }

    public function root(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
        ]);
    }
}
