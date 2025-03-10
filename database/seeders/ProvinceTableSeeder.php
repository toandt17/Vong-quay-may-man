<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ProvinceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::retry(3, 300)
            ->get('https://esgoo.net/api-tinhthanh/1/0.htm');
        $data = $response->json();

        if ($data['error'] === 0) {
            foreach ($data['data'] as $province) {
                Province::updateOrCreate([
                    'id' => $province['id']
                ], [
                    'name' => $province['name']
                ]);
            }
        }
    }
}
