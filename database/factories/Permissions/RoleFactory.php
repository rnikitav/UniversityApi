<?php

namespace Database\Factories\Permissions;

use Illuminate\Database\Eloquent\Factories\Factory;
use function fake;

class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function mock(): RoleFactory
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->name(),
            'guard_name' => 'api'
        ]);
    }
}
