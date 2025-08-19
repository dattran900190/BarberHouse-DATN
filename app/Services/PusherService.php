<?php

namespace App\Services;

use Pusher\Pusher;
use App\Models\Appointment;
use App\Models\Service;

class PusherService
{
    protected $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            ['cluster' => config('broadcasting.connections.pusher.options.cluster'), 'useTLS' => true]
        );
    }

    /**
     * Gửi thông báo lịch hẹn mới đến admin
     */
    public function triggerAppointmentCreated(Appointment $appointment)
    {
        $additionalServiceIds = json_decode($appointment->additional_services ?? '[]', true);
        $additionalServicesNames = Service::whereIn('id', $additionalServiceIds)->pluck('name')->toArray();

        $pusherData = [
            'id' => $appointment->id,
            'appointment_code' => $appointment->appointment_code,
            'user_name' => $appointment->name ?? 'N/A',
            'phone' => $appointment->phone ?? 'N/A',
            'barber_name' => $appointment->barber->name ?? 'N/A',
            'service_name' => $appointment->service->name ?? 'N/A',
            'status' => $appointment->status,
            'payment_status' => $appointment->payment_status,
            'created_at' => $appointment->created_at->format('d/m/Y H:i'),
            'additional_services' => $additionalServicesNames ?? [],
            'payment_method' => $appointment->payment_method,
            'appointment_time' => $appointment->appointment_time->format('d/m/Y H:i'),
            'branch_id' => $appointment->branch_id,
        ];

        // Gửi đến channel chung cho admin chính (có thể xem tất cả chi nhánh)
        $this->pusher->trigger('appointments', 'App\\Events\\AppointmentCreated', $pusherData);
        
        // Gửi đến channel riêng biệt cho chi nhánh cụ thể
        $branchChannel = 'branch.' . $appointment->branch_id;
        $this->pusher->trigger($branchChannel, 'App\\Events\\AppointmentCreated', $pusherData);
    }

    /**
     * Gửi thông báo tùy chỉnh đến channel cụ thể
     */
    public function triggerCustomEvent($channel, $event, $data)
    {
        $this->pusher->trigger($channel, $event, $data);
    }
}
