<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new Channel('orders');
    }

    public function broadcastWith()
    {
        return [
            'message' => 'Có đơn hàng mới từ ' . ($this->order->name ?? 'Khách hàng không xác định'),
            'order_id' => $this->order->id,
        ];
    }
} 