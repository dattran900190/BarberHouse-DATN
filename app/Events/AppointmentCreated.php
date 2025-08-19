<?php

namespace App\Events;

use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

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
        return [
            new Channel('appointments'), // Channel chung cho admin chính
            new Channel('branch.' . $this->appointment->branch_id), // Channel riêng cho chi nhánh
        ];
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
            'additional_services' => Service::whereIn(
                json_decode($this->appointment->additional_services ?? '[]'),
                []
            )->pluck('name'),
            'payment_method' => $this->appointment->payment_method ?? 'undefined',
            'branch_id' => $this->appointment->branch_id, // Thêm branch_id
        ];
    }
}
