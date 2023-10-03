<?php

namespace Database\Factories\Accelerator;

use App\Models\Accelerator\AcceleratorStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use function fake;

class AcceleratorFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function mock(): AcceleratorFactory
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'published_at' => fake()->date(),
            'date_end_accepting' => fake()->date(),
            'date_end' => fake()->date(),
            'status_id' => AcceleratorStatus::notPublished(),
        ]);
    }
}
