<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductCategoryFactory extends Factory
{
    protected $model = \App\Models\ProductCategory::class;

    public function definition()
    {
        static $index = 0;
        $categories = [
            ['name' => 'Dầu gội', 'slug' => 'dau-goi', 'description' => 'Các loại dầu gội dành cho mọi loại tóc.'],
            ['name' => 'Dầu xả', 'slug' => 'dau-xa', 'description' => 'Dầu xả giúp tóc mềm mại và óng mượt.'],
            ['name' => 'Sản phẩm tạo kiểu', 'slug' => 'san-pham-tao-kieu', 'description' => 'Gel, sáp và các sản phẩm tạo kiểu tóc.'],
            ['name' => 'Chăm sóc tóc', 'slug' => 'cham-soc-toc', 'description' => 'Sản phẩm phục hồi và bảo vệ tóc.'],
        ];

        $category = $categories[$index % count($categories)];
        $index++;

        return [
            'name' => $category['name'],
            'slug' => $category['slug'],
            'description' => $category['description'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}