<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Order::class;

    public function definition()
    {
        return [
            'order_code' => 'OD' . $this->faker->unique()->numberBetween(100000, 999999),
            'user_id' => null, // hoặc gán user_id hợp lệ nếu cần
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'total_money' => 0, // sẽ tính sau
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipping', 'completed', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['cash', 'momo', 'vnpay', 'card']),
            'note' => $this->faker->sentence,
        ];
    }
}
