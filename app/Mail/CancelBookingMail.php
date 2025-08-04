<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class CancelBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    public function __construct($appointment)
    {
        $this->appointment = is_object($appointment) ? $appointment : (object) $appointment;
    }

    public function build()
    {
        return $this->subject('Lịch hẹn của bạn huỷ thành công')
            ->view('emails.cancel_booking')
            ->with(['appointment' => $this->appointment]);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cancel_booking',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
