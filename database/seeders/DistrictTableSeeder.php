<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class DistrictTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = Province::all();
        foreach ($provinces as $province) {
            $response = Http::retry(3, 300)->get("https://esgoo.net/api-tinhthanh/2/{$province->id}.htm");
            $data = $response->json();

            if ($data['error'] === 0) {
                foreach ($data['data'] as $district) {
                    District::updateOrCreate([
                        'id' => $district['id']
                    ], [
                        'name' => $district['name'],
                        'province_id' => $province->id
                    ]);
                }
            }
        }
    }
}
