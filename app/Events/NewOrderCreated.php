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
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'name' => $this->order->name,
            'phone' => $this->order->phone,
            'address' => $this->order->address,
            'total_money' => $this->order->total_money,
            'payment_method' => $this->order->payment_method,
            'payment_status' => $this->order->payment_status ?? 'unpaid',
            'created_at' => $this->order->created_at ? $this->order->created_at->format('d/m/Y H:i') : '',
            'message' => 'Có đơn hàng mới từ ' . ($this->order->name ?? 'Khách hàng không xác định'),
        ];
    }
} 