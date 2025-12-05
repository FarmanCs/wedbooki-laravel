<?php

namespace App\Mail\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorAcceptBookingMail extends Mailable
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
            subject: 'Booking Accepted'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor.vendor_accepted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
