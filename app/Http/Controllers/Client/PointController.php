<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PointHistory;
use App\Models\Promotion;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            ->where('required_points', '>', 0)
            ->whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('user_redeemed_vouchers')
                    ->whereColumn('user_redeemed_vouchers.promotion_id', 'promotions.id')
                    ->where('user_redeemed_vouchers.user_id', $user->id);
            })
            ->paginate(8);

        return view('client.redeem', compact('promotions'));
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

            return redirect()->route('client.redeem')
                ->with('success', 'Bạn đã đổi mã giảm giá thành công! Mã giảm giá đã được thêm vào tài khoản của bạn.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
