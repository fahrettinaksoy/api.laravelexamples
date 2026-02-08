<?php

declare(strict_types=1);

namespace Database\Factories\Content\Page;

use App\Models\Content\Page\PageModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content\Page\PageModel>
 */
class PageFactory extends Factory
{
    protected $model = PageModel::class;

    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(5, true),
            'excerpt' => fake()->paragraph(),
            'is_active' => fake()->boolean(80), // 80% active
            'published_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-1 year', 'now') : null,
            'meta_title' => fake()->sentence(8),
            'meta_description' => fake()->sentence(15),
            'meta_keywords' => implode(', ', fake()->words(5)),
        ];
    }

    /**
     * Indicate that the page is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the page is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'published_at' => null,
        ]);
    }
}
