<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(30)->create();
        \App\Models\Barber::factory(10)->create();

        //  Tạo review
        \App\Models\Review::factory(30)->create();

        \App\Models\Payment::factory(10)->create();

        \App\Models\Appointment::factory(10)->create();

        \App\Models\Service::factory(10)->create();

        \App\Models\Branch::factory(10)->create();
    }
}
