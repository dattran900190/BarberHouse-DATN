<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
          return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->numberBetween(50000, 500000),
            'duration' => $this->faker->numberBetween(15, 120),
            'is_combo' => $this->faker->boolean,
            'image' => null, // Không có ảnh
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
