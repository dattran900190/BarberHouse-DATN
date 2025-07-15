<?php

namespace App\Events;

use App\Models\RefundRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefundRequestCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $refundRequest;

    public function __construct(RefundRequest $refundRequest)
    {
        $this->refundRequest = $refundRequest;
    }

    public function broadcastOn()
    {
        return new Channel('admin.refunds');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->refundRequest->id,
            'user_name' => $this->refundRequest->user->name,
            'order_code' => $this->refundRequest->order->order_code ?? null,
            'appointment_code' => $this->refundRequest->appointment->appointment_code ?? null,
            'refund_amount' => number_format($this->refundRequest->refund_amount, 0, ',', '.'),
            'refund_status' => $this->refundRequest->refund_status,
            'created_at' => $this->refundRequest->created_at->format('d/m/Y H:i'),
            'reason' => $this->refundRequest->reason,
        ];
    }

    public function broadcastAs()
    {
        return 'refund.created';
    }
}