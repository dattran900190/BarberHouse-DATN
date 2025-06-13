<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use Illuminate\Http\Request;

class PointHistoryController extends Controller
{
    /**
     * Hiển thị danh sách lịch sử điểm (có tìm kiếm theo tên user và phân trang).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $histories = PointHistory::with(['user', 'promotion', 'appointment'])
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.point_histories.index', compact('histories', 'search'));
    }

    /**
     * Hiển thị chi tiết lịch sử điểm.
     */
    public function show(PointHistory $pointHistory)
    {
        // Load các quan hệ: user, promotion, appointment
        $pointHistory->load(['user', 'promotion', 'appointment']);

        return view('admin.point_histories.show', compact('pointHistory'))->with('title', 'Chi tiết lịch sử điểm');
    }

    /**
     * Xóa một lịch sử điểm (nếu cần).
     */
    public function destroy(PointHistory $pointHistory)
    {
        $pointHistory->delete();

        return redirect()->route('point_histories.index')->with('success', 'Xóa lịch sử điểm thành công.');
    }
}
