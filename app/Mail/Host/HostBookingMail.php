<?php

namespace App\Mail\Host;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HostBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $timeDetails;

    public function __construct($booking, $timeDetails)
    {
        $this->booking = $booking;
        $this->timeDetails = $timeDetails;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Booking Has Been Accepted'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.host.host_accepted'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
