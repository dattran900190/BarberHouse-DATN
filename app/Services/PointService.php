<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\PointHistory;
use App\Models\User;
use App\Models\Promotion;
use App\Models\UserRedeemedVoucher;
use Illuminate\Support\Facades\DB;

class PointService
{
    public function earnPoints(Appointment $appointment)
    {
        if ($appointment->status !== 'completed' || $appointment->payment_status !== 'paid') {
            return false;
        }
        if (PointHistory::where('appointment_id', $appointment->id)->where('type', 'earned')->exists()) {
            return false; // Đã tích điểm earned cho lịch hẹn này
        }
        $user = $appointment->user;
        $service = $appointment->service;

        // Tính điểm: 100.000 VNĐ = 10 điểm
        $points = floor($service->price / 100000) * config('points.points_per_100k', 10);

        DB::transaction(function () use ($user, $points, $appointment) {
            $user->increment('points_balance', $points);
            PointHistory::create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => 'earned',
                'appointment_id' => $appointment->id,
                'created_at' => now(),
            ]);
        });

        return true;
    }

    public function redeemPoints(User $user, Promotion $promotion)
    {
        if ($user->points_balance < $promotion->required_points) {
            throw new \Exception('Không đủ điểm để đổi mã giảm giá.');
        }

        if (!$promotion->is_active || $promotion->quantity <= 0 || now()->lt($promotion->start_date) || now()->gt($promotion->end_date)) {
            throw new \Exception('Mã giảm giá không hợp lệ hoặc đã hết hạn.');
        }

        $exists = UserRedeemedVoucher::where('user_id', $user->id)
            ->where('promotion_id', $promotion->id)
            ->exists();

        if ($exists) {
            throw new \Exception('Bạn đã đổi mã này rồi!');
        }

        DB::transaction(function () use ($user, $promotion) {
            $user->decrement('points_balance', $promotion->required_points);
            PointHistory::create([
                'user_id' => $user->id,
                'points' => -$promotion->required_points,
                'type' => 'redeemed',
                'promotion_id' => $promotion->id,
                'created_at' => now(),
            ]);
            UserRedeemedVoucher::create([
                'user_id' => $user->id,
                'promotion_id' => $promotion->id,
                'redeemed_at' => now(),
                'is_used' => false,
            ]);
            $promotion->decrement('quantity');
        });

        return true;
    }
}
