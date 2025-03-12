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
        $this->call([
            // Chạy các seeder địa chỉ trước
            ProvinceTableSeeder::class,
            DistrictTableSeeder::class,
            WardTableSeeder::class,

            // Sau đó chạy các seeder khác
            UserSeeder::class,
            PrizeSeeder::class,
            // CategorySeeder::class,
            // ProductSeeder::class,
        ]);
    }
}
