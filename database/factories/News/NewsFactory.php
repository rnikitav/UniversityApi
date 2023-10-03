<?php

namespace Database\Factories\News;

use App\Models\News\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use function fake;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{

    public function definition(): array
    {
        return [];
    }
    public function mock(): NewsFactory
    {
        return $this->state(fn (array $attributes) => [
            'title' => fake()->sentence(4),
            'body' => fake()->randomHtml(2,3),
            'slug' => fake()->slug,
            'published_at' => fake()->dateTime(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ]);
    }
}
