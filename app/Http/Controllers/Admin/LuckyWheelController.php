<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LuckyWheel;
use Illuminate\Http\Request;

class LuckyWheelController extends Controller
{
    /**
     * Hiển thị danh sách vòng quay may mắn.
     */
    public function index()
    {
        $luckyWheels = LuckyWheel::latest()->paginate(10);
        return view('admin.lucky_wheel.index', compact('luckyWheels'));
    }

    /**
     * Hiển thị form tạo vòng quay mới.
     */
    public function create()
    {
        return view('admin.lucky_wheel.create');
    }

    /**
     * Lưu vòng quay mới vào database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        LuckyWheel::create($validated);

        return redirect()->route('admin.lucky-wheels.index')
            ->with('success', 'Vòng quay may mắn đã được tạo thành công.');
    }

    /**
     * Hiển thị thông tin chi tiết vòng quay.
     */
    public function show(LuckyWheel $luckyWheel)
    {
        return view('admin.lucky_wheel.show', compact('luckyWheel'));
    }

    /**
     * Hiển thị form chỉnh sửa vòng quay.
     */
    public function edit(LuckyWheel $luckyWheel)
    {
        return view('admin.lucky_wheel.edit', compact('luckyWheel'));
    }

    /**
     * Cập nhật thông tin vòng quay.
     */
    public function update(Request $request, LuckyWheel $luckyWheel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $luckyWheel->update($validated);

        return redirect()->route('admin.lucky-wheels.index')
            ->with('success', 'Vòng quay may mắn đã được cập nhật thành công.');
    }

    /**
     * Xóa vòng quay.
     */
    public function destroy(LuckyWheel $luckyWheel)
    {
        $luckyWheel->delete();

        return redirect()->route('admin.lucky-wheels.index')
            ->with('success', 'Vòng quay may mắn đã được xóa thành công.');
    }
}
