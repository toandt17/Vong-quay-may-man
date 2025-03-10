<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LuckyWheel;
use App\Models\Prize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrizeController extends Controller
{
    /**
     * Hiển thị danh sách giải thưởng.
     */
    public function index(LuckyWheel $luckyWheel)
    {
        $prizes = $luckyWheel->prizes()->paginate(10);
        return view('admin.lucky_wheel.prize.index', compact('luckyWheel', 'prizes'));
    }

    /**
     * Hiển thị form tạo giải thưởng mới.
     */
    public function create(LuckyWheel $luckyWheel)
    {
        return view('admin.lucky_wheel.prize.create', compact('luckyWheel'));
    }

    /**
     * Lưu giải thưởng mới vào database.
     */
    public function store(Request $request, LuckyWheel $luckyWheel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'win_rate' => 'required|numeric|min:0|max:100',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('prizes', 'public');
            $validated['image'] = $path;
        }

        $validated['lucky_wheel_id'] = $luckyWheel->id;
        $validated['remaining'] = $validated['quantity'];

        Prize::create($validated);

        return redirect()->route('admin.lucky-wheels.prizes.index', $luckyWheel)
            ->with('success', 'Giải thưởng đã được tạo thành công.');
    }

    /**
     * Hiển thị thông tin chi tiết giải thưởng.
     */
    public function show(LuckyWheel $luckyWheel, Prize $prize)
    {
        return view('admin.lucky_wheel.prize.show', compact('luckyWheel', 'prize'));
    }

    /**
     * Hiển thị form chỉnh sửa giải thưởng.
     */
    public function edit(LuckyWheel $luckyWheel, Prize $prize)
    {
        return view('admin.lucky_wheel.prize.edit', compact('luckyWheel', 'prize'));
    }

    /**
     * Cập nhật thông tin giải thưởng.
     */
    public function update(Request $request, LuckyWheel $luckyWheel, Prize $prize)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'win_rate' => 'required|numeric|min:0|max:100',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($prize->image) {
                Storage::disk('public')->delete($prize->image);
            }

            $path = $request->file('image')->store('prizes', 'public');
            $validated['image'] = $path;
        }

        // Cập nhật số lượng còn lại nếu số lượng thay đổi
        if ($prize->quantity != $validated['quantity']) {
            $diff = $validated['quantity'] - $prize->quantity;
            $validated['remaining'] = $prize->remaining + $diff;
        }

        $prize->update($validated);

        return redirect()->route('admin.lucky-wheels.prizes.index', $luckyWheel)
            ->with('success', 'Giải thưởng đã được cập nhật thành công.');
    }

    /**
     * Xóa giải thưởng.
     */
    public function destroy(LuckyWheel $luckyWheel, Prize $prize)
    {
        // Xóa ảnh nếu có
        if ($prize->image) {
            Storage::disk('public')->delete($prize->image);
        }

        $prize->delete();

        return redirect()->route('admin.lucky-wheels.prizes.index', $luckyWheel)
            ->with('success', 'Giải thưởng đã được xóa thành công.');
    }
}
