<?php

namespace Database\Seeders;

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
        // Gọi seeder thủ công (nếu có)
        $this->call([
            BranchSeeder::class,
            
        ]);

        // Tạo dữ liệu mẫu bằng factory
        User::factory(30)->create();
        \App\Models\Barber::factory(10)->create();
        \App\Models\Review::factory(30)->create();
        \App\Models\Payment::factory(10)->create();
        \App\Models\Appointment::factory(10)->create();
        \App\Models\Service::factory(10)->create();
        \App\Models\Branch::factory(10)->create();
        \App\Models\Product::factory(10)->create();
        \App\Models\ProductVariant::factory(10)->create();
    }
}
