<?php

declare(strict_types=1);

namespace Database\Factories\Catalog\Product\Subs;

use App\Models\Catalog\Product\Subs\ProductTranslationModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalog\Product\Subs\ProductTranslationModel>
 */
class ProductTranslationFactory extends Factory
{
    protected $model = ProductTranslationModel::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'locale' => 'tr',
            'name' => ucfirst($name),
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(20),
            'meta_title' => ucfirst($name).' - '.fake()->words(3, true),
            'meta_description' => fake()->sentence(15),
            'meta_keywords' => implode(', ', fake()->words(5)),
        ];
    }

    /**
     * Turkish translation.
     */
    public function turkish(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'tr',
        ]);
    }

    /**
     * English translation.
     */
    public function english(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'en',
        ]);
    }

    /**
     * German translation.
     */
    public function german(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'de',
        ]);
    }
}
