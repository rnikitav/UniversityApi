<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function mock(): self
    {
        $filePath = fake()->file(base_path(), storage_path('app/testing'));
        $name = str_replace(storage_path('app/testing') . '/', '', $filePath);
        return $this->state(fn (array $attributes) => [
            'disk' => 'testing',
            'category' => fake()->name(),
            'path' => $name,
            'original_name' => fake()->name(),
            'sha256' => hash_file('sha256', $filePath),
        ]);
    }
}
