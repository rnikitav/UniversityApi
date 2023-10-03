<?php

namespace Database\Factories\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcceleratorCaseParticipantFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function mock(int $userId): AcceleratorCaseParticipantFactory
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
            'role_id' => AcceleratorCaseRole::owner(),
        ]);
    }
}
