<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Tòn',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        // You can add more users here if needed
    }
}
