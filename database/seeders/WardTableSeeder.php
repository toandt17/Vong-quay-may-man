<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WardTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = District::all();
        foreach ($districts as $district) {
            $response = Http::retry(3, 300)->get("https://esgoo.net/api-tinhthanh/3/{$district->id}.htm");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['error']) && $data['error'] === 0 && isset($data['data'])) {
                    foreach ($data['data'] as $ward) {
                        Ward::updateOrCreate([
                            'id' => $ward['id']
                        ], [
                            'name' => $ward['name'],
                            'district_id' => $district->id
                        ]);
                    }
                } else {
                    Log::warning("API returned an error or invalid data for district ID: {$district->id}");
                }
            } else {
                Log::error("Failed to fetch wards for district ID: {$district->id}, HTTP status: " . $response->status());
            }
        }
    }
}
