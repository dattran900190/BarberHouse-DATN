<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition()
    {
        $products = [
            ['name' => 'Dầu gội Head & Shoulders', 'category' => 1, 'description' => 'Dầu gội trị gàu hiệu quả, phù hợp với mọi loại tóc.', 'price' => 150000.00, 'image' => 'head_shoulders.jpg'],
            ['name' => 'Dầu gội Dove', 'category' => 1, 'description' => 'Dầu gội dưỡng ẩm cho tóc khô và xỉn màu.', 'price' => 120000.00, 'image' => 'dove_shampoo.jpg'],
            ['name' => 'Dầu xả Sunsilk', 'category' => 2, 'description' => 'Dầu xả giúp tóc mềm mượt và dễ chải.', 'price' => 130000.00, 'image' => 'sunsilk_conditioner.jpg'],
            ['name' => 'Dầu xả TRESemmé', 'category' => 2, 'description' => 'Dầu xả phục hồi tóc hư tổn.', 'price' => 180000.00, 'image' => 'tresemme_conditioner.jpg'],
            ['name' => 'Sáp vuốt tóc Gatsby', 'category' => 3, 'description' => 'Sáp tạo kiểu giữ nếp mạnh, không bết dính.', 'price' => 200000.00, 'image' => 'gatsby_wax.jpg'],
        ];

        $product = $this->faker->randomElement($products);

        return [
            'product_category_id' => $product['category'], // Giả sử ID danh mục đã được tạo
            'name' => $product['name'],
            'description' => $product['description'],
            'price' => $product['price'],
            'stock' => $this->faker->numberBetween(50, 100),
            'image' => $product['image'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}