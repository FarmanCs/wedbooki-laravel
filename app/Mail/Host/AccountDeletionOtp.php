<?php

namespace App\Mail\Host;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeletionOtp extends Mailable
{
    use Queueable, SerializesModels;

    public string $fullName;
    public string $otp;

    /**
     * Create a new message instance.
     */
    public function __construct(string $fullName, string $otp)
    {
        $this->fullName = $fullName;
        $this->otp = $otp;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Deletion OTP',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.host.account_deletion_otp',
            with: [
                'fullName' => $this->fullName,
                'otp' => $this->otp,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
