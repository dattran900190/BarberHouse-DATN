<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Tạo 4 danh mục sản phẩm
        $categories = ProductCategory::factory(4)->create();

        // Với mỗi danh mục, tạo 3–5 sản phẩm
        foreach ($categories as $category) {
            $products = Product::factory(rand(3, 5))->create([
                'product_category_id' => $category->id,
            ]);

            // Với mỗi sản phẩm, tạo 2–4 biến thể
            foreach ($products as $product) {
                ProductVariant::factory(rand(2, 4))->create([
                    'product_id' => $product->id,
                ]);
            }
        }

        // Tạo dữ liệu mẫu bằng factory
        User::factory(30)->create();
        \App\Models\Barber::factory(10)->create();
        \App\Models\Review::factory(30)->create();
        \App\Models\Payment::factory(10)->create();
        \App\Models\Appointment::factory(10)->create();
        \App\Models\Service::factory(10)->create();
        \App\Models\Branch::factory(10)->create();

        Order::factory()
            ->count(10)
            ->create()
            ->each(function ($order) {
                $items = OrderItem::factory()->count(rand(2, 5))->make();
                $total = 0;

                foreach ($items as $item) {
                    $item->order_id = $order->id;
                    $item->save();
                    $total += $item->total_price;
                }

                $order->update(['total_money' => $total]);
            });
    }
}
