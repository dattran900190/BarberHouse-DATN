<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PointHistory;
use App\Models\Promotion;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointController extends Controller
{
    protected PointService $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

    /**
     * Hiển thị form đổi điểm sang voucher.
     */
    public function redeemForm()
    {
        $user = Auth::user(); 

        // Lấy danh sách promotion đủ điều kiện (có số lượng, trong thời hạn, và điểm user đủ)
        $promotions = Promotion::where('is_active', true)
            ->where('quantity', '>', 0)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('required_points', '<=', $user->points_balance) // 👈 chỉ hiện cái user đổi được
            ->get();

        return view('client.points.redeem', compact('promotions'));
    }

    /**
     * Xử lý khi người dùng đổi điểm sang voucher.
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'promotion_id' => 'required|exists:promotions,id',
        ]);

        try {
            $user = Auth::user();
            $promotion = Promotion::findOrFail($request->promotion_id);

            $this->pointService->redeemPoints($user, $promotion);

            return redirect()->route('cai-dat-tai-khoan', ['tab' => 'account-point-history'])
                ->with('success', 'Đổi điểm thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
