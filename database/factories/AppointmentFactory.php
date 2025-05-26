<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Barber;
use App\Models\Branch;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? null,
            'barber_id' => Barber::inRandomOrder()->first()?->id ?? null,
            'service_id' => Service::inRandomOrder()->first()?->id ?? null,
            'branch_id' => Branch::inRandomOrder()->first()?->id ?? null,
            'appointment_time' => $this->faker->dateTimeBetween('+1 days', '+1 week'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            'payment_status' => $this->faker->randomElement(['unpaid', 'paid']),
            'note' => $this->faker->optional()->sentence(),
            'is_free' => $this->faker->boolean(10), // 10% là miễn phí
            'promotion_id' => null, // vì chưa có bảng promotions
            'discount_amount' => 0,
        ];
    }
}
