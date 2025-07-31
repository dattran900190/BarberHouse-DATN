<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $statusType;

    public function __construct(Order $order, $statusType = 'success')
    {
        $this->order = $order;
        $this->statusType = $statusType;
    }

    public function build()
    {
        $subject = $this->getSubject();
        
        return $this->subject($subject)
            ->view('emails.order_success')
            ->with(['order' => $this->order]);
    }

    private function getSubject()
    {
        switch ($this->statusType) {
            case 'processing':
                return 'Xác nhận đơn hàng tại Barber House';
            case 'shipping':
                return 'Đơn hàng của bạn đang được giao - Barber House';
            case 'completed':
                return 'Đơn hàng của bạn đã hoàn thành - Barber House';
            case 'cancelled':
                return 'Đơn hàng của bạn đã bị hủy - Barber House';
            default:
                return 'Cập nhật trạng thái đơn hàng - Barber House';
        }
    }
} 