<?php

namespace Database\Factories\User;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use function fake;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function external(): UserFactory
    {
        return $this->state(fn (array $attributes) => [
            'external' => true
        ]);
    }

    public function unverified(): UserFactory
    {
        return $this->state(fn (array $attributes) => [
            'confirm_token' => Str::random(40),
        ]);
    }

    public function mock(): UserFactory
    {
        return $this->state(fn (array $attributes) => [
            'login' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'external' => false
        ]);
    }
}
