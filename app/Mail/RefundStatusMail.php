<?php

namespace App\Mail;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $refund;
    public $status;

    public function __construct(RefundRequest $refund, string $status)
    {
        $this->refund = $refund;
        $this->status = $status;
    }

    public function build()
    {
        $subject = $this->status === 'refunded'
            ? 'Thông báo hoàn tiền thành công'
            : 'Thông báo từ chối yêu cầu hoàn tiền';

        return $this->subject($subject)
                    ->view('emails.refund_status')
                    ->with([
                        'user' => $this->refund->user,
                        'order' => $this->refund->order,
                        'refund' => $this->refund,
                        'status' => $this->status,
                    ]);
    }
}