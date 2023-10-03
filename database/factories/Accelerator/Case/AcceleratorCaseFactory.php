<?php

namespace Database\Factories\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseParticipation;
use Illuminate\Database\Eloquent\Factories\Factory;
use function fake;

class AcceleratorCaseFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function accelerator(int $id): AcceleratorCaseFactory
    {
        return $this->state(fn (array $attributes) => [
            'accelerator_id' => $id,
        ]);
    }

    public function status(string $id): AcceleratorCaseFactory
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => $id,
        ]);
    }

    public function mock(): AcceleratorCaseFactory
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->name(),
            'description' => fake()->date(),
            'participation_id' => AcceleratorCaseParticipation::single(),
        ]);
    }
}
