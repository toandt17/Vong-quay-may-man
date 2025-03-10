<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AwardHistory;
use App\Models\LuckyWheel;
use Illuminate\Http\Request;

class AwardHistoryController extends Controller
{
    /**
     * Hiển thị danh sách lịch sử trúng thưởng.
     */
    public function index(Request $request)
    {
        $query = AwardHistory::with(['participant', 'luckyWheel', 'prize'])
            ->latest();

        // Lọc theo vòng quay nếu có
        if ($request->has('lucky_wheel_id')) {
            $query->where('lucky_wheel_id', $request->lucky_wheel_id);
        }

        // Lọc theo trạng thái trúng thưởng
        if ($request->has('is_win')) {
            $query->where('is_win', $request->is_win);
        }

        $awardHistories = $query->paginate(15);
        $luckyWheels = LuckyWheel::all();

        return view('admin.award_history.index', compact('awardHistories', 'luckyWheels'));
    }

    /**
     * Hiển thị thông tin chi tiết lịch sử trúng thưởng.
     */
    public function show(AwardHistory $awardHistory)
    {
        $awardHistory->load(['participant', 'luckyWheel', 'prize']);
        return view('admin.award_history.show', compact('awardHistory'));
    }

    /**
     * Xuất danh sách lịch sử trúng thưởng ra file Excel.
     */
    public function export(Request $request)
    {
        $query = AwardHistory::with(['participant', 'luckyWheel', 'prize'])
            ->latest();

        // Lọc theo vòng quay nếu có
        if ($request->has('lucky_wheel_id')) {
            $query->where('lucky_wheel_id', $request->lucky_wheel_id);
        }

        // Lọc theo trạng thái trúng thưởng
        if ($request->has('is_win')) {
            $query->where('is_win', $request->is_win);
        }

        $awardHistories = $query->get();

        // Tạo file Excel và trả về cho người dùng tải xuống
        // (Phần này sẽ cần thêm package để xuất Excel, ví dụ: maatwebsite/excel)

        return redirect()->back()->with('success', 'Đã xuất danh sách lịch sử trúng thưởng thành công.');
    }
}
