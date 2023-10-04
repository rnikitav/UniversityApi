<?php

namespace Database\Factories\Accelerator;

use Illuminate\Database\Eloquent\Factories\Factory;
use function fake;

class AcceleratorControlPointFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function accelerator(int $id): AcceleratorControlPointFactory
    {
        return $this->state(fn (array $attributes) => [
            'accelerator_id' => $id,
        ]);
    }

    public function mock(): AcceleratorControlPointFactory
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->name(),
            'date_completion' => fake()->date(),
            'max_score' => fake()->numberBetween(1, 100),
        ]);
    }
}
