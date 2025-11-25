<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SignupOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $otp;

    /**
     * Create a new message instance.
     */
    public function __construct($name,$otp)
    {
        $this->name = $name;
        $this->otp = $otp;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your WedBookie Signup OTP',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.signup-otp',
            with: [
                'name' => $this->name,
                'otp' => $this->otp,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
