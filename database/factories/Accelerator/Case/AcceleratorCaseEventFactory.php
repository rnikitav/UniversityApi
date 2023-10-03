<?php

namespace Database\Factories\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseEventStatus;
use App\Models\Accelerator\Case\AcceleratorCaseEventType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcceleratorCaseEventFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function case(int $id): AcceleratorCaseEventFactory
    {
        return $this->state(fn (array $attributes) => [
            'case_id' => $id,
        ]);
    }

    public function exit(): AcceleratorCaseEventFactory
    {
        return $this->state(fn (array $attributes) => [
            'type_id' => AcceleratorCaseEventType::exit(),
        ]);
    }

    public function mock(int $userId): AcceleratorCaseEventFactory
    {
        return $this->state(fn (array $attributes) => [
            'initializer_id' => $userId,
            'type_id' => AcceleratorCaseEventType::enter(),
            'description' => fake()->text(),
            'participant_id' => $userId,
            'status_id' => AcceleratorCaseEventStatus::submitted(),
        ]);
    }
}
