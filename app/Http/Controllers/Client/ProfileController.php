<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\PointHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        // Lấy user đang đăng nhập
        $user = Auth::user();

        // Lấy lịch sử điểm của user đó
        $pointHistories = PointHistory::with('promotion') // nếu có liên kết với promotion
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('client.profile', compact('user', 'pointHistories'));
    }
}
