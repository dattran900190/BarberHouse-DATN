<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        // Sử dụng private channel riêng cho từng user
        return new PrivateChannel('user.' . $this->order->user_id);
    }

    public function broadcastWith()
    {
        // Mảng ánh xạ trạng thái tiếng Anh sang tiếng Việt
        $statusTranslations = [
            'pending' => 'Đang chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
        ];

        // Lấy trạng thái đã dịch, mặc định trả về trạng thái gốc nếu không tìm thấy
        $translatedStatus = $statusTranslations[$this->order->status] ?? $this->order->status;

        return [
            'message' => 'Đơn hàng của bạn đã được cập nhật trạng thái thành ' . $translatedStatus,
            'order_id' => $this->order->id,
            'status' => $translatedStatus, // Trả về trạng thái đã dịch
        ];
    }

    public function broadcastAs()
    {
        return 'OrderStatusUpdated';
    }
}