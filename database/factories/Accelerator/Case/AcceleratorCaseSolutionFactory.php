<?php

namespace Database\Factories\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseSolutionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcceleratorCaseSolutionFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function case(int $id): AcceleratorCaseSolutionFactory
    {
        return $this->state(fn (array $attributes) => [
            'case_id' => $id,
        ]);
    }

    public function point(int $id): AcceleratorCaseSolutionFactory
    {
        return $this->state(fn (array $attributes) => [
            'control_point_id' => $id,
        ]);
    }

    public function mock(int $userId): AcceleratorCaseSolutionFactory
    {
        return $this->state(fn (array $attributes) => [
            'author_id' => $userId,
            'description' => fake()->text(),
            'score' => fake()->numberBetween(1, 10),
            'status_id' => AcceleratorCaseSolutionStatus::submitted(),
        ]);
    }
}
