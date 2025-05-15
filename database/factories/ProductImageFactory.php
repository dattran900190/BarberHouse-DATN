<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = \App\Models\ProductImage::class;

    public function definition()
    {
        return [
            'product_id' => \App\Models\Product::factory(), // Tạo sản phẩm liên quan
            'image_url' => $this->faker->randomElement([
                'product_image_1.jpg',
                'product_image_2.jpg',
                'product_image_3.jpg',
            ]),
        ];
    }
}