<?php
namespace App\Services;

use App\Models\Appointment;
use App\Models\UserRedeemedVoucher;

class AppointmentService
{
    public function applyPromotion(Appointment $appointment, UserRedeemedVoucher $voucher)
    {
        // Kiểm tra mã đã sử dụng chưa
        if ($voucher->is_used) {
            throw new \Exception('Mã giảm giá đã được sử dụng.');
        }

        $promotion = $voucher->promotion;
        $service = $appointment->service;

        // Kiểm tra mã giảm giá hợp lệ
        if (!$promotion->is_active || $promotion->quantity <= 0 || now()->lt($promotion->start_date) || now()->gt($promotion->end_date)) {
            throw new \Exception('Mã giảm giá không hợp lệ hoặc đã hết hạn.');
        }

        // Kiểm tra giá trị đơn hàng tối thiểu
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

        // Cập nhật appointment
        $appointment->update([
            'promotion_id' => $promotion->id,
            'discount_amount' => $discount,
        ]);

        // Đánh dấu voucher đã sử dụng
        $voucher->update([
            'is_used' => true,
            'used_at' => now(),
        ]);

        return $discount;
    }
}