<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    $status = $this->faker->randomElement(['pending', 'paid']);

    return [
        'order_id' => Order::inRandomOrder()->first()?->id ?? null,
        'method' => $this->faker->randomElement(['momo', 'cash']),
        'amount' => $this->faker->randomFloat(2, 50, 500),
        'status' => $status,
        'transaction_code' => $status === 'paid' ? $this->faker->unique()->regexify('TXN[0-9]{6}') : null,
        'paid_at' => $status === 'paid' ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
        'created_at' => now(),
        'updated_at' => now(),
    ];
}

}
