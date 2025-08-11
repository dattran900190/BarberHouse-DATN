<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $appointment;

    public function __construct($otp, $appointment = null)
    {
        $this->otp = $otp;
        $this->appointment = $appointment;
    }

    public function build()
    {
        return $this->subject('Mã OTP xác nhận đặt lịch')
                    ->view('emails.appointment_otp')
                    ->with([
                        'otp' => $this->otp,
                        'appointment' => $this->appointment,
                    ]);
    }
}
