<?php

namespace App\Mail\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignupOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $otp;

    public function __construct($name, $otp)
    {
        $this->name = $name;
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Verify Your Email - OTP')
            ->view('emails.signup-otp')
            ->with([
                'name' => $this->name,
                'otp'  => $this->otp,
            ]);
    }
}
