<?php

namespace Database\Factories\Accelerator\Case;

use Illuminate\Database\Eloquent\Factories\Factory;

class AcceleratorCaseScoreFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function case(int $id): AcceleratorCaseScoreFactory
    {
        return $this->state(fn (array $attributes) => [
            'case_id' => $id,
        ]);
    }

    public function mock(int $userId): AcceleratorCaseScoreFactory
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
            'score' => fake()->numberBetween(1, 10),
        ]);
    }
}
