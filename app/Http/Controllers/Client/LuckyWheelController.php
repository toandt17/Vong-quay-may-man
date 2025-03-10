<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AwardHistory;
use App\Models\LuckyWheel;
use App\Models\Participant;
use App\Models\Prize;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LuckyWheelController extends Controller
{
    /**
     * Hiển thị trang chủ vòng quay may mắn.
     */
    public function index()
    {
        // Lấy vòng quay đang hoạt động
        $luckyWheel = LuckyWheel::where('is_active', true)->first();

        if (!$luckyWheel) {
            return view('client.lucky_wheel.maintenance');
        }

        // Lấy danh sách giải thưởng với đầy đủ thông tin
        $prizes = $luckyWheel->prizes->map(function ($prize) {
            return [
                'id' => $prize->id,
                'name' => $prize->name,
                'image' => $prize->image ? asset('storage/' . $prize->image) : null,
                'background_color' => $prize->background_color,
                'icon' => $prize->icon,
                'win_rate' => $prize->win_rate,
                'quantity' => $prize->quantity,
                'remaining' => $prize->remaining,
                'description' => $prize->description,
            ];
        });

        return view('client.index', compact('luckyWheel', 'prizes'));
    }

    /**
     * Xử lý đăng ký thông tin người tham gia.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'is_farmer' => 'boolean',
            'rice_variety' => 'nullable|string|max:255',
            'used_products' => 'nullable|string|max:255',
        ]);

        // Kiểm tra xem số điện thoại đã tồn tại chưa
        $participant = Participant::where('phone', $validated['phone'])->first();

        if ($participant) {
            // Kiểm tra xem đã quay chưa
            if ($participant->hasSpun()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số điện thoại này đã được sử dụng để tham gia vòng quay.',
                ]);
            }
        } else {
            // Tạo người tham gia mới
            $participant = Participant::create($validated);
        }

        return response()->json([
            'success' => true,
            'participant_id' => $participant->id,
            'message' => 'Đăng ký thành công! Bạn có thể quay vòng quay may mắn ngay bây giờ.',
        ]);
    }

    /**
     * Xử lý quay vòng quay may mắn.
     */
    public function spin(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'lucky_wheel_id' => 'required|exists:lucky_wheels,id',
        ]);

        $participant = Participant::findOrFail($request->participant_id);
        $luckyWheel = LuckyWheel::findOrFail($request->lucky_wheel_id);

        // Kiểm tra xem người dùng đã quay chưa
        if ($participant->hasSpun()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã sử dụng lượt quay của mình.',
            ]);
        }

        // Lấy danh sách giải thưởng có số lượng còn lại > 0
        $availablePrizes = $luckyWheel->prizes()->where('remaining', '>', 0)->get();

        // Nếu không còn giải thưởng nào
        if ($availablePrizes->isEmpty()) {
            // Tạo lịch sử không trúng thưởng
            AwardHistory::create([
                'participant_id' => $participant->id,
                'lucky_wheel_id' => $luckyWheel->id,
                'prize_id' => null,
                'spin_time' => Carbon::now(),
                'is_win' => false,
            ]);

            return response()->json([
                'success' => true,
                'is_win' => false,
                'message' => 'Rất tiếc, bạn không trúng thưởng.',
            ]);
        }

        // Tính toán giải thưởng dựa trên tỷ lệ
        $prize = $this->calculatePrize($availablePrizes);

        // Bắt đầu transaction để đảm bảo tính nhất quán dữ liệu
        DB::beginTransaction();

        try {
            // Nếu trúng thưởng
            if ($prize) {
                // Giảm số lượng giải thưởng còn lại
                $prize->decrement('remaining');

                // Tạo lịch sử trúng thưởng
                AwardHistory::create([
                    'participant_id' => $participant->id,
                    'lucky_wheel_id' => $luckyWheel->id,
                    'prize_id' => $prize->id,
                    'spin_time' => Carbon::now(),
                    'is_win' => true,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'is_win' => true,
                    'prize' => [
                        'id' => $prize->id,
                        'name' => $prize->name,
                        'image' => $prize->image ? asset('storage/' . $prize->image) : null,
                        'description' => $prize->description,
                        'background_color' => $prize->background_color,
                        'icon' => $prize->icon,
                        'win_rate' => $prize->win_rate,
                        'quantity' => $prize->quantity,
                        'remaining' => $prize->remaining - 1, // Đã giảm 1 ở trên
                    ],
                    'message' => 'Chúc mừng! Bạn đã trúng ' . $prize->name,
                ]);
            } else {
                // Tạo lịch sử không trúng thưởng
                AwardHistory::create([
                    'participant_id' => $participant->id,
                    'lucky_wheel_id' => $luckyWheel->id,
                    'prize_id' => null,
                    'spin_time' => Carbon::now(),
                    'is_win' => false,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'is_win' => false,
                    'message' => 'Rất tiếc, bạn không trúng thưởng.',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi quay thưởng. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    /**
     * Tính toán giải thưởng dựa trên tỷ lệ.
     */
    private function calculatePrize($prizes)
    {
        // Tổng tỷ lệ của tất cả giải thưởng
        $totalRate = $prizes->sum('win_rate');

        // Nếu tổng tỷ lệ > 100%, điều chỉnh lại
        $adjustmentFactor = $totalRate > 100 ? 100 / $totalRate : 1;

        // Tạo mảng tỷ lệ tích lũy
        $cumulativeRates = [];
        $cumulativeRate = 0;

        foreach ($prizes as $prize) {
            $adjustedRate = $prize->win_rate * $adjustmentFactor;
            $cumulativeRate += $adjustedRate;
            $cumulativeRates[$prize->id] = $cumulativeRate;
        }

        // Tạo số ngẫu nhiên từ 0 đến tổng tỷ lệ (tối đa 100)
        $randomNumber = mt_rand(0, 10000) / 100; // Để có 2 chữ số thập phân

        // Nếu số ngẫu nhiên lớn hơn tổng tỷ lệ, không trúng thưởng
        if ($randomNumber > min(100, $totalRate)) {
            return null;
        }

        // Xác định giải thưởng dựa trên số ngẫu nhiên
        foreach ($cumulativeRates as $prizeId => $rate) {
            if ($randomNumber <= $rate) {
                return $prizes->firstWhere('id', $prizeId);
            }
        }

        return null;
    }
}
