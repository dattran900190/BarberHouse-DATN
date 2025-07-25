<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
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
        // Sử dụng private channel riêng cho từng user
        return new PrivateChannel('user.' . $this->appointment->user_id);
    }

    public function broadcastWith()
    {
        // Mảng ánh xạ trạng thái tiếng Anh sang tiếng Việt
        $statusTranslations = [
            'pending' => 'Đang chờ xử lý',
            'unconfirmed' => 'Chưa xác nhận',
            'confirmed' => 'Đã xác nhận',
            'checked-in' => 'Đã đến',
            'progress' => 'Đang thực hiện',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
        ];

        // Lấy trạng thái đã dịch, mặc định trả về trạng thái gốc nếu không tìm thấy
        $translatedStatus = $statusTranslations[$this->appointment->status] ?? $this->appointment->status;

        return [
            'message' => 'Lịch hẹn của bạn đã được cập nhật trạng thái thành ' . $translatedStatus,
            'appointment_id' => $this->appointment->id,
            'status' => $translatedStatus, // Trả về trạng thái đã dịch
        ];
    }

    public function broadcastAs()
    {
        return 'AppointmentStatusUpdated';
    }
}
