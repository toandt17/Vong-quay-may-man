<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Lấy danh sách tất cả các tỉnh/thành phố
     */
    public function provinces(): JsonResponse
    {
        $provinces = Province::orderBy('name')->get();
        return response()->json([
            'success' => true,
            'data' => $provinces
        ]);
    }

    /**
     * Lấy danh sách quận/huyện theo tỉnh/thành phố
     */
    public function districts(Province $province): JsonResponse
    {
        $districts = $province->districts()->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'data' => $districts
        ]);
    }

    /**
     * Lấy danh sách phường/xã theo quận/huyện
     */
    public function wards(District $district): JsonResponse
    {
        $wards = $district->wards()->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'data' => $wards
        ]);
    }
}
