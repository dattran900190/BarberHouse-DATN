<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $additionalServices;
    public $expirationTime;

    public function __construct(Appointment $appointment, array $additionalServices = [])
    {
        $this->appointment = $appointment;
        $this->additionalServices = $additionalServices;
        $this->expirationTime = $appointment->confirmation_token_expires_at->format('H:i d/m/Y');
    }

    public function build()
    {
        $url = route('confirm.booking', ['token' => $this->appointment->confirmation_token]);
        return $this->view('emails.confirm_booking')
                    ->with([
                        'url' => $url,
                        'appointment' => $this->appointment,
                        'additionalServices' => $this->additionalServices,
                        'expirationTime' => $this->expirationTime,
                    ])
                    ->subject('Xác nhận lịch hẹn của bạn');
    }
}