<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Services\PointService;

class AppointmentObserver
{
    public function updated(Appointment $appointment)
    {
        // Kiểm tra nếu status đổi thành 'completed' VÀ payment_status là 'paid'
        if (
            $appointment->wasChanged('status') &&
            $appointment->status === 'completed' &&
            $appointment->payment_status === 'paid'
        ) {
            $pointService = new PointService();
            $pointService->earnPoints($appointment);
        }
    }
}
