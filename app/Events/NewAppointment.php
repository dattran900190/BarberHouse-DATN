<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewAppointment implements ShouldBroadcast
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
            'message' => 'Có lịch hẹn mới từ ' . ($this->appointment->name ?? 'Khách hàng không xác định'),
            'appointment_id' => $this->appointment->id,
            'branch_id' => $this->appointment->branch_id, // Thêm branch_id để admin chi nhánh có thể lọc
        ];
    }

    public function broadcastAs()
    {
        return 'NewAppointment';
    }
}
