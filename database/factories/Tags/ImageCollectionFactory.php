<?php

namespace Database\Factories\Tags;

use Illuminate\Database\Eloquent\Factories\Factory;

class ImageCollectionFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function mock(): ImageCollectionFactory
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->name(),
        ]);
    }
}
