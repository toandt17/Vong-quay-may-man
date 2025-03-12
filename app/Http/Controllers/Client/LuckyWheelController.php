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
use Illuminate\Support\Facades\Log;

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
            'rice_variety' => 'required_if:is_farmer,1|nullable|string|max:255',
            'rice_stage' => 'required_if:is_farmer,1|nullable|string|max:255',
            'used_products' => 'nullable',
        ]);

        // Kiểm tra xem số điện thoại đã tồn tại chưa
        $participant = Participant::where('phone', $validated['phone'])->first();

        if ($participant) {
            // Kiểm tra xem đã quay chưa
            if ($participant->hasSpun()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số điện thoại này đã được sử dụng. Vui lòng dùng số khác.',
                    'error_type' => 'phone_used'
                ], 400);
            }

            // Cập nhật thông tin cho người tham gia hiện tại
            $participant->update($validated);

            // Thông báo cho người dùng biết số điện thoại đã tồn tại nhưng vẫn cho phép tiếp tục
            $message = 'Số điện thoại này đã được đăng ký. Thông tin của bạn đã được cập nhật.';
        } else {
            // Tạo người tham gia mới
            $participant = Participant::create($validated);
            $message = 'Đăng ký thành công! Bạn có thể quay vòng quay may mắn ngay bây giờ.';
        }

        // Lấy vòng quay hiện tại
        $luckyWheel = LuckyWheel::where('is_active', true)->first();

        if (!$luckyWheel) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy vòng quay may mắn.',
            ]);
        }

        // Lấy danh sách giải thưởng có số lượng còn lại > 0
        $availablePrizes = $luckyWheel->prizes()->where('remaining', '>', 0)->get();

        // Log số lượng giải thưởng còn available cho debug
        Log::info('Số lượng giải thưởng còn available: ' . $availablePrizes->count());
        Log::info('Danh sách giải thưởng còn available:', $availablePrizes->map(function ($prize) {
            return [
                'id' => $prize->id,
                'name' => $prize->name,
                'remaining' => $prize->remaining,
                'win_rate' => $prize->win_rate
            ];
        })->toArray());

        // Bắt đầu quay ngầm và lưu kết quả
        DB::beginTransaction();
        try {
            // Nếu không còn giải thưởng nào
            if ($availablePrizes->isEmpty()) {
                Log::warning('Không còn giải thưởng nào available.');

                // Tạo lịch sử không trúng thưởng
                $awardHistory = AwardHistory::create([
                    'participant_id' => $participant->id,
                    'lucky_wheel_id' => $luckyWheel->id,
                    'prize_id' => null,
                    'spin_time' => Carbon::now(),
                    'is_win' => false,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'participant_id' => $participant->id,
                    'lucky_wheel_id' => $luckyWheel->id,
                    'pre_determined_result' => [
                        'is_win' => false,
                        'message' => 'Rất tiếc, bạn không trúng thưởng.',
                    ],
                    'message' => $message,
                    'is_existed' => $participant->wasRecentlyCreated ? false : true,
                ]);
            }

            // Tính toán giải thưởng dựa trên tỷ lệ - CHỈ với các giải còn available
            // Đặt forceWin=true để khuyến khích hệ thống chọn một giải thưởng nếu có thể
            $prize = $this->calculatePrize($availablePrizes, true);

            if ($prize) {
                Log::info('Quay ngầm đã chọn giải thưởng:', [
                    'id' => $prize->id,
                    'name' => $prize->name,
                    'remaining' => $prize->remaining,
                    'win_rate' => $prize->win_rate
                ]);
            } else {
                Log::info('Quay ngầm không trúng giải nào.');
            }

            // Nếu trúng thưởng
            if ($prize) {
                // Kiểm tra lại xem giải thưởng có còn available không
                if ($prize->remaining <= 0) {
                    Log::warning('Giải thưởng đã hết nhưng vẫn được chọn. ID: ' . $prize->id);

                    // Nếu hết rồi, kiểm tra lại danh sách giải available
                    $remainingPrizes = $luckyWheel->prizes()->where('remaining', '>', 0)->get();

                    if ($remainingPrizes->isEmpty()) {
                        Log::warning('Tất cả giải thưởng đã hết.');

                        // Tạo lịch sử không trúng thưởng
                        $awardHistory = AwardHistory::create([
                            'participant_id' => $participant->id,
                            'lucky_wheel_id' => $luckyWheel->id,
                            'prize_id' => null,
                            'spin_time' => Carbon::now(),
                            'is_win' => false,
                        ]);

                        DB::commit();

                        return response()->json([
                            'success' => true,
                            'participant_id' => $participant->id,
                            'lucky_wheel_id' => $luckyWheel->id,
                            'pre_determined_result' => [
                                'is_win' => false,
                                'message' => 'Rất tiếc, bạn không trúng thưởng.',
                            ],
                            'message' => $message,
                            'is_existed' => $participant->wasRecentlyCreated ? false : true,
                        ]);
                    } else {
                        // Chọn giải thưởng ngẫu nhiên khác còn available
                        $totalRate = $remainingPrizes->sum('win_rate');
                        $randomPrizeIndex = mt_rand(0, $remainingPrizes->count() - 1);
                        $prize = $remainingPrizes[$randomPrizeIndex];

                        Log::info('Chọn lại giải thưởng khác còn available:', [
                            'id' => $prize->id,
                            'name' => $prize->name,
                            'remaining' => $prize->remaining
                        ]);
                    }
                }

                // Giảm số lượng giải thưởng còn lại
                $prize->decrement('remaining');

                // Tạo lịch sử trúng thưởng
                $awardHistory = AwardHistory::create([
                    'participant_id' => $participant->id,
                    'lucky_wheel_id' => $luckyWheel->id,
                    'prize_id' => $prize->id,
                    'spin_time' => Carbon::now(),
                    'is_win' => true,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'participant_id' => $participant->id,
                    'lucky_wheel_id' => $luckyWheel->id,
                    'pre_determined_result' => [
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
                            'remaining' => $prize->remaining - 1, // Hiển thị giá trị sau khi đã giảm
                        ],
                        'message' => 'Chúc mừng! ' . $prize->name,
                    ],
                    'message' => $message,
                    'is_existed' => $participant->wasRecentlyCreated ? false : true,
                ]);
            } else {
                // Tạo lịch sử không trúng thưởng
                $awardHistory = AwardHistory::create([
                    'participant_id' => $participant->id,
                    'lucky_wheel_id' => $luckyWheel->id,
                    'prize_id' => null,
                    'spin_time' => Carbon::now(),
                    'is_win' => false,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'participant_id' => $participant->id,
                    'lucky_wheel_id' => $luckyWheel->id,
                    'pre_determined_result' => [
                        'is_win' => false,
                        'message' => 'Rất tiếc, bạn không trúng thưởng.',
                    ],
                    'message' => $message,
                    'is_existed' => $participant->wasRecentlyCreated ? false : true,
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xử lý quay ngầm: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xử lý thông tin. Vui lòng thử lại sau.',
                'error' => $e->getMessage()
            ], 500);
        }
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

        // Lấy lịch sử quay của người dùng
        $awardHistory = AwardHistory::where('participant_id', $participant->id)
                                   ->where('lucky_wheel_id', $luckyWheel->id)
                                   ->first();

        // Nếu không tìm thấy lịch sử, nghĩa là người dùng chưa "quay ngầm" khi đăng ký
        if (!$awardHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin quay thưởng. Vui lòng thử lại.',
            ]);
        }

        // Trả về kết quả đã được xác định trước
        if ($awardHistory->is_win) {
            $prize = $awardHistory->prize;

            if (!$prize) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin giải thưởng. Vui lòng thử lại.',
                ]);
            }

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
                    'remaining' => $prize->remaining,
                ],
                'message' => 'Chúc mừng! ' . $prize->name,
            ]);
        } else {
            return response()->json([
                'success' => true,
                'is_win' => false,
                'message' => 'Rất tiếc, bạn không trúng thưởng.',
            ]);
        }
    }

    /**
     * Tính toán giải thưởng dựa trên tỷ lệ.
     *
     * @param \Illuminate\Database\Eloquent\Collection $prizes Danh sách giải thưởng
     * @param bool $forceWin Có bắt buộc trúng thưởng không (nếu còn giải)
     * @return \App\Models\Prize|null
     */
    private function calculatePrize($prizes, $forceWin = false)
    {
        // Chỉ xét các giải thưởng còn số lượng
        $availablePrizes = $prizes->filter(function($prize) {
            return $prize->remaining > 0;
        });

        // Nếu không còn giải thưởng nào, trả về null
        if ($availablePrizes->isEmpty()) {
            Log::warning('Không còn giải thưởng nào có số lượng > 0.');
            return null;
        }

        // Tổng tỷ lệ của tất cả giải thưởng còn available
        $totalRate = $availablePrizes->sum('win_rate');

        // Nếu tổng tỷ lệ > 100%, điều chỉnh lại
        $adjustmentFactor = $totalRate > 100 ? 100 / $totalRate : 1;

        // Tạo mảng tỷ lệ tích lũy
        $cumulativeRates = [];
        $cumulativeRate = 0;

        foreach ($availablePrizes as $prize) {
            $adjustedRate = $prize->win_rate * $adjustmentFactor;
            $cumulativeRate += $adjustedRate;
            $cumulativeRates[$prize->id] = $cumulativeRate;
        }

        // Log thông tin tỷ lệ
        Log::info('Tỷ lệ tích lũy của các giải còn available:', $cumulativeRates);

        // Tạo số ngẫu nhiên từ 0 đến tổng tỷ lệ (tối đa 100)
        $randomNumber = mt_rand(0, 10000) / 100; // Để có 2 chữ số thập phân
        Log::info('Số ngẫu nhiên: ' . $randomNumber . ' / Tổng tỷ lệ: ' . min(100, $totalRate));

        // Nếu forceWin = true và có giải thưởng, đảm bảo số ngẫu nhiên nằm trong phạm vi để trúng thưởng
        if ($forceWin && !$availablePrizes->isEmpty() && $randomNumber > min(100, $totalRate)) {
            $randomNumber = mt_rand(0, (int)($totalRate * 100)) / 100;
            Log::info('Đã điều chỉnh số ngẫu nhiên để bắt buộc trúng thưởng: ' . $randomNumber);
        }

        // Nếu số ngẫu nhiên lớn hơn tổng tỷ lệ, không trúng thưởng
        if ($randomNumber > min(100, $totalRate)) {
            Log::info('Không trúng thưởng vì số ngẫu nhiên > tổng tỷ lệ');
            return null;
        }

        // Xác định giải thưởng dựa trên số ngẫu nhiên
        foreach ($cumulativeRates as $prizeId => $rate) {
            if ($randomNumber <= $rate) {
                $winningPrize = $availablePrizes->firstWhere('id', $prizeId);
                Log::info('Đã chọn giải thưởng: ' . $winningPrize->name . ' (ID: ' . $prizeId . ')');
                return $winningPrize;
            }
        }

        // Nếu đến đây mà vẫn chưa chọn được giải thưởng, chọn một giải ngẫu nhiên (safe fallback)
        if ($forceWin && !$availablePrizes->isEmpty()) {
            $randomPrize = $availablePrizes->random();
            Log::warning('Không thể xác định giải thưởng dựa trên tỷ lệ, chọn ngẫu nhiên: ' . $randomPrize->name);
            return $randomPrize;
        }

        return null;
    }
}
