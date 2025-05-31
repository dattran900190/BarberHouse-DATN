<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $price = $this->faker->randomFloat(2, 100, 500);

        return [
            'product_variant_id' => ProductVariant::inRandomOrder()->first()?->id ?? 1,
            'quantity' => $quantity,
            'price_at_time' => $price,
            'total_price' => $quantity * $price,
        ];
    }
}
