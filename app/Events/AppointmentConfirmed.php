<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmed implements ShouldBroadcast
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
            'message' => 'Lịch hẹn #' . $this->appointment->id . ' đã được xác nhận',
            'appointment_id' => $this->appointment->id,
        ];
    }

    public function broadcastAs()
    {
        return 'AppointmentConfirmed';
    }
}