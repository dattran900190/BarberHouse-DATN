<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BarberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'profile' => $this->faker->paragraph,
            'skill_level' => $this->faker->randomElement(['Beginner', 'Intermediate', 'Expert']),
            'avatar' => $this->faker->imageUrl(200, 200, 'people'),
            'rating_avg' => $this->faker->randomFloat(1, 1, 5),
        ];
    }
}
