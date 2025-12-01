<?php

namespace App\Mail\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $otp;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $otp)
    {
        $this->name = $name;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Forget Password OTP')
            ->view('emails.Vendor.forget-password')
            ->with([
                'name' => $this->name,
                'otp'  => $this->otp,
            ]);
    }
}
