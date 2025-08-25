<?php

namespace App\Jobs;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CleanExpiredAppointments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Xóa các lịch hẹn unconfirmed cũ hơn 10 phút
        $expiredAppointments = Appointment::where('status', 'unconfirmed')
            ->where('created_at', '<', Carbon::now()->subMinutes(10))
            ->get();

        foreach ($expiredAppointments as $appointment) {
            // Log thông tin trước khi xóa
            Log::info('Xóa lịch hẹn hết hạn: ' . $appointment->appointment_code);
            $appointment->delete();
        }

        // Xóa các lịch hẹn VNPay unconfirmed cũ hơn 10 phút (thanh toán nhanh hơn)
        $expiredVnpayAppointments = Appointment::where('status', 'unconfirmed')
            ->where('payment_method', 'vnpay')
            ->where('created_at', '<', Carbon::now()->subMinutes(10))
            ->get();

        foreach ($expiredVnpayAppointments as $appointment) {
            // Log thông tin trước khi xóa
            Log::info('Xóa lịch hẹn VNPay hết hạn: ' . $appointment->appointment_code);
            $appointment->delete();
        }
    }
}