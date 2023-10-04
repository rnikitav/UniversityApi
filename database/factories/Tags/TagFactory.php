<?php

namespace Database\Factories\Tags;

use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function mock(): TagFactory
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->name(),
        ]);
    }
}
