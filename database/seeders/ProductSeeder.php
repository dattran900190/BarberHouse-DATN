<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Tạo 4 danh mục sản phẩm
        \App\Models\ProductCategory::factory()->count(4)->create();

        // Tạo 4 khối lượng
        \App\Models\Volume::factory()->count(4)->create();

        // Tạo 10 sản phẩm, mỗi sản phẩm có 1-3 biến thể và 1-2 hình ảnh
        \App\Models\Product::factory()
            ->count(10)
            ->has(\App\Models\ProductVariant::factory()->count(rand(1, 3)))
            ->has(\App\Models\ProductImage::factory()->count(rand(1, 2)))
            ->create();
    }
}