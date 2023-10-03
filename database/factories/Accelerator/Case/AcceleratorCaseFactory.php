<?php

namespace database\factories\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseParticipation;
use Illuminate\Database\Eloquent\Factories\Factory;
use function fake;

class AcceleratorCaseFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function grouped(): AcceleratorCaseFactory
    {
        return $this->state(fn (array $attributes) => [
            'participation_id' => AcceleratorCaseParticipation::group(),
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
