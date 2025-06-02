<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productVariant = ProductVariant::inRandomOrder()->first() ?? ProductVariant::factory()->create();

        return [
            'cart_id' => Cart::inRandomOrder()->first() ?? Cart::factory()->create(),
            'product_variant_id' => $productVariant->id,
            'quantity' => $this->faker->numberBetween(1, 10),
            'price' => $productVariant->price,
        ];
    }
}