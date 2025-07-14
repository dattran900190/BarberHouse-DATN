<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentCreated implements ShouldBroadcast
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
            'id' => $this->appointment->id,
            'appointment_code' => $this->appointment->appointment_code,
            'user_name' => $this->appointment->name ?? 'N/A', // Đổi key cho đúng JS
            'phone' => $this->appointment->phone ?? 'N/A',
            'barber_name' => $this->appointment->barber->name ?? 'N/A',
            'service_name' => $this->appointment->service->name ?? 'N/A',
            'status' => $this->appointment->status,
            'payment_status' => $this->appointment->payment_status,
            'created_at' => $this->appointment->created_at->format('d/m/Y H:i'),
        ];
    }
}
