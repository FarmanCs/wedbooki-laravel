<?php

namespace App\Mail\Host;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HostBookingCancelMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $timeDetails;

    /**
     * Create a new message instance.
     */
    public function __construct($booking, $timeDetails)
    {
        $this->booking = $booking;
        $this->timeDetails = $timeDetails;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Booking Has Been Cancelled'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.host.host_booking_cancel'
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
