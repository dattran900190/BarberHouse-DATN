<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = \App\Models\ProductVariant::class;

    public function definition()
    {
        return [
            'product_id' => \App\Models\Product::factory(), // Tạo sản phẩm liên quan
            'volume_id' => \App\Models\Volume::factory(), // Tạo khối lượng liên quan
            'name' => $this->faker->randomElement(['Nhỏ', 'Trung bình', 'Lớn']),
            'description' => $this->faker->sentence(8),
            'price' => $this->faker->randomFloat(2, 100000, 300000),
            'stock' => $this->faker->numberBetween(20, 80),
            'image' => $this->faker->randomElement(['variant_small.jpg', 'variant_medium.jpg', 'variant_large.jpg']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}