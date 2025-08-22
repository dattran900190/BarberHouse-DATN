<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminCompletedBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $additionalServices;

    public function __construct(Appointment $appointment, array $additionalServices = [])
    {
        $this->appointment = $appointment;
        $this->additionalServices = $additionalServices;
    }

    public function build()
    {
        return $this->view('emails.admin_completed_booking')
                    ->with([
                        'appointment' => $this->appointment,
                        'additionalServices' => $this->additionalServices,
                    ])
                    ->subject('Lịch hẹn của bạn đã hoàn thành - Barber House');
    }
}