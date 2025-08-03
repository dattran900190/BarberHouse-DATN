<?php

namespace App\Mail;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

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

        $mail = $this->subject($subject)
                     ->view('emails.refund_status')
                     ->with([
                         'user' => $this->refund->user,
                         'order' => $this->refund->order,
                         'appointment' => $this->refund->appointment,
                         'refund' => $this->refund,
                         'status' => $this->status,
                         'reject_reason' => $this->status === 'rejected' ? $this->refund->reject_reason : null,
                     ]);

        // Đính kèm hình ảnh minh chứng nếu có
        if ($this->status === 'refunded' && $this->refund->proof_image) {
            $filePath = storage_path('app/public/' . $this->refund->proof_image);
            if (file_exists($filePath)) {
                $mail->attach($filePath, [
                    'as' => 'proof_image.' . pathinfo($filePath, PATHINFO_EXTENSION),
                    'mime' => mime_content_type($filePath),
                ]);
            }
        }

        return $mail;
    }
}