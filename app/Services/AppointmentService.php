<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\UserRedeemedVoucher;
use App\Models\Promotion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function applyPromotion(Appointment $appointment, string $promotionCode)
    {
        // Tìm promotion theo code
        $promotion = Promotion::where('code', $promotionCode)
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        if (!$promotion) {
            throw new \Exception('Mã giảm giá không hợp lệ.');
        }

        // Kiểm tra mã đổi bằng điểm
        $voucher = null;
        if ($promotion->required_points) {
            $voucher = UserRedeemedVoucher::where('user_id', Auth::id())
                ->where('promotion_id', $promotion->id)
                ->where('is_used', false)
                ->first();

            if (!$voucher) {
                throw new \Exception('Bạn không sở hữu mã giảm giá này hoặc mã đã được sử dụng.');
            }
        }

        // Kiểm tra min_order_value
        $service = $appointment->service;
        if ($promotion->min_order_value && $service->price < $promotion->min_order_value) {
            throw new \Exception('Giá trị đơn hàng không đủ để áp dụng mã giảm giá.');
        }

        // Tính toán giảm giá
        $discount = 0;
        if ($promotion->discount_type === 'fixed') {
            $discount = $promotion->discount_value;
        } elseif ($promotion->discount_type === 'percent') {
            $discount = ($promotion->discount_value / 100) * $service->price;
            if ($promotion->max_discount_amount && $discount > $promotion->max_discount_amount) {
                $discount = $promotion->max_discount_amount;
            }
        }

        DB::transaction(function () use ($appointment, $promotion, $voucher, $discount) {
            // Cập nhật appointment
            $appointment->update([
                'promotion_id' => $promotion->id,
                'discount_amount' => $discount,
                'total_amount' => $appointment->service->price - $discount,
            ]);

            // Cập nhật voucher nếu có
            if ($voucher) {
                $voucher->update([
                    'is_used' => true,
                    'used_at' => now(),
                ]);
            }

            // Giảm số lượng promotion
            // ✅ Chỉ trừ quantity nếu không phải mã đổi điểm (tức là không có $voucher)
            if (!$voucher) {
                $promotion->decrement('quantity');
            }
        });

        return $discount;
    }
}
