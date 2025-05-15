<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VolumeFactory extends Factory
{
    protected $model = \App\Models\Volume::class;

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['100ml', '250ml', '500ml', '1000ml']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}