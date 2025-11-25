<?php

namespace App\Mail\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignupOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $otp;

    public function __construct($email, $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Verify Your Email - OTP')
            ->view('emails.signup-otp')
            ->with([
                'email' => $this->email,
                'otp' => $this->otp
            ]);
    }
}
