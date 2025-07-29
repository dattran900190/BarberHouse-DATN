<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use App\Models\User;
use Illuminate\Http\Request;

class PointHistoryController extends Controller
{
    /**
     * Hiển thị danh sách lịch sử điểm (có tìm kiếm theo tên user và phân trang).
     */
    // Sửa lại method index: chỉ lấy danh sách người dùng
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.point_histories.index', compact('users', 'search'));
    }

    // Trang hiển thị lịch sử điểm của user cụ thể
    public function userHistory($userId)
    {
        $user = User::findOrFail($userId);

        $pointHistories = PointHistory::with(['promotion', 'appointment'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.point_histories.user_history', compact('user', 'pointHistories'));
    }
}
