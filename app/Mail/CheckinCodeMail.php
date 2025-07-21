<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckinCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $appointment;
    public $additionalServices;

    public function __construct($code, $appointment, array $additionalServices = [])
    {
        $this->code = $code;
        $this->appointment = $appointment;
        $this->additionalServices = $additionalServices;
    }

    public function build()
    {
        return $this->view('emails.checkin_code')
            ->with([
                'code' => $this->code,
                'appointment' => $this->appointment,
                'additionalServices' => $this->additionalServices,
            ])->subject('Mã Check-in của bạn');
    }
}
