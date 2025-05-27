<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Dầu gội',
            'Dầu xả',
            'Sản phẩm tạo kiểu',
            'Chăm sóc tóc',
        ]);

        return [
            'name' => $this->faker->company,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
