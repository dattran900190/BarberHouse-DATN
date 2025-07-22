<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function broadcastOn()
    {
        return new Channel('appointments');
    }

    public function broadcastWith()
    {
        return [
            'message' => 'Lịch hẹn của ' . ($this->appointment->name ?? 'Khách hàng không xác định') . ' đã được cập nhật trạng thái thành ' . $this->appointment->status,
            'appointment_id' => $this->appointment->id,
            'status' => $this->appointment->status,
        ];
    }

    public function broadcastAs()
    {
        return 'AppointmentStatusUpdated';
    }
}