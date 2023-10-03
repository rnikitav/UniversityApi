<?php

namespace Database\Factories\News;

use Illuminate\Database\Eloquent\Factories\Factory;
use function fake;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News\News>
 */
class NewsFactory extends Factory
{

    public function definition()
    {
        return [];
    }
    public function mock(): NewsFactory
    {
        return $this->state(fn (array $attributes) => [
            'title' => fake()->sentence(4),
            'body' => fake()->randomHtml(2,3),
            'slug' => fake()->slug,
            'img_preview' => '/img/' . fake()->text(15) . fake()->fileExtension(),
            'img' => '/img/' . fake()->text(15) . fake()->fileExtension(),
            'published_at' => fake()->dateTime(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ]);
    }
}
