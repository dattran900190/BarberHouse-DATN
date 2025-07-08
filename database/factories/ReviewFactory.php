<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Barber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? 1,
            'barber_id' => Barber::inRandomOrder()->first()?->id ?? 1,
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence(),
            'is_visible' => 1, // true = hiển thị, false = ẩn
            'appointment_id' => Appointment::inRandomOrder()->first()?->id ?? null, // nếu có sau này thì thêm vào
        ];
    }
}
