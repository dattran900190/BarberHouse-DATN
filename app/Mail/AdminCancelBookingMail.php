<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminCancelBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    public function __construct($appointment)
    {
        $this->appointment = is_object($appointment) ? $appointment : (object) $appointment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông Báo Hủy Lịch Hẹn ' . $this->appointment->appointment_code . ' - Barber House',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_cancel_booking',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
