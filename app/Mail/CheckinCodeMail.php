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

    public function __construct($code, $appointment)
    {
        $this->code = $code;
        $this->appointment = $appointment;
    }

    public function build()
    {
        return $this->subject('Mã Check-in của bạn')
                    ->view('emails.checkin_code');
    }
}
