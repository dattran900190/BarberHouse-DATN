<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\UserRedeemedVoucher;
use App\Models\Promotion;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    /**
     * Áp dụng mã giảm giá vào lịch hẹn
     *
     * @param Appointment $appointment
     * @param UserRedeemedVoucher|null $redeemedVoucher
     * @param Promotion|null $promotion
     * @return float Giảm giá đã áp dụng
     * @throws \Exception Nếu mã không hợp lệ hoặc không đủ điều kiện
     */
    public function applyPromotion(Appointment $appointment, ?UserRedeemedVoucher $redeemedVoucher = null, ?Promotion $promotion = null)
    {
        // Nếu có voucher đổi điểm, thì lấy promotion từ đó
        if (!$promotion && $redeemedVoucher) {
            $promotion = $redeemedVoucher->promotion;
        }

        if (!$promotion) {
            throw new \Exception('Không tìm thấy mã giảm giá hợp lệ.');
        }

        // Kiểm tra tình trạng hợp lệ của promotion
        if (!$promotion->is_active || $promotion->quantity <= 0 || now()->lt($promotion->start_date) || now()->gt($promotion->end_date)) {
            throw new \Exception('Mã giảm giá không còn hiệu lực.');
        }

        // Kiểm tra điều kiện đơn hàng tối thiểu
        $service = $appointment->service;

        // Tính tổng giá dịch vụ chính + dịch vụ bổ sung
        $totalServicePrice = $service->price;

        // Nếu có dịch vụ bổ sung, cộng thêm giá
        if (!empty($appointment->additional_services)) {
            // Nếu lưu dạng JSON
            $additionalServiceIds = is_array($appointment->additional_services)
                ? $appointment->additional_services
                : json_decode($appointment->additional_services, true);

            if ($additionalServiceIds && is_array($additionalServiceIds)) {
                $additionalPrices = \App\Models\Service::whereIn('id', $additionalServiceIds)->pluck('price')->sum();
                $totalServicePrice += $additionalPrices;
            }
        }

        // Kiểm tra điều kiện đơn hàng tối thiểu
        if ($promotion->min_order_value && $totalServicePrice < $promotion->min_order_value) {
            throw new \Exception('Giá trị đơn hàng không đủ để áp dụng mã giảm giá.');
        }

        // Tính toán giảm giá
        $discount = 0;
        if ($promotion->discount_type === 'fixed') {
            $discount = $promotion->discount_value;
        } elseif ($promotion->discount_type === 'percent') {
            $discount = ($promotion->discount_value / 100) * $totalServicePrice;
            if ($promotion->max_discount_amount && $discount > $promotion->max_discount_amount) {
                $discount = $promotion->max_discount_amount;
            }
        }

        // Bắt đầu transaction
        DB::transaction(function () use ($appointment, $promotion, $redeemedVoucher, $discount, $totalServicePrice) {
            // Cập nhật lịch hẹn
            $appointment->update([
                'promotion_id' => $promotion->id,
                'discount_amount' => $discount,
                'total_amount' => $totalServicePrice - $discount,
            ]);

            // Nếu có voucher, cập nhật đã sử dụng
            if ($redeemedVoucher) {
                $redeemedVoucher->update([
                    'is_used' => true,
                    'used_at' => now(),
                ]);
            }

            // Nếu là promotion công khai (không có voucher) thì giảm quantity
            if (!$redeemedVoucher) {
                $promotion->decrement('quantity');
            }
        });

        return $discount;
    }
}
