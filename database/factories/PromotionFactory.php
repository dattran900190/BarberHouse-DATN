<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    public function definition(): array
    {
        $discountType = $this->faker->randomElement(['fixed', 'percent']);

        return [
            'code' => strtoupper($this->faker->unique()->bothify('PROMO###')),
            'description' => $this->faker->sentence(10),
            'required_points' => $this->faker->optional()->numberBetween(0, 100),
            'usage_limit' => $this->faker->numberBetween(1, 10),
            'discount_type' => $discountType,
            'discount_value' => $discountType === 'fixed'
                ? $this->faker->numberBetween(5000, 50000)
                : $this->faker->numberBetween(5, 50),
            'max_discount_amount' => $discountType === 'percent' ? $this->faker->numberBetween(10000, 50000) : null,
            'min_order_value' => $this->faker->optional()->numberBetween(100000, 300000),
            'quantity' => $this->faker->numberBetween(10, 100),
            'start_date' => now()->subDays(rand(1, 10))->toDateString(),
            'end_date' => now()->addDays(rand(5, 20))->toDateString(),
            'is_active' => '1'
        ];
    }
}
