<?php

namespace App\Mail\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vendor;
    public $business;
    public $formattedTime;
    public $hostName;

    /**
     * Create a new message instance.
     */
    public function __construct($vendor, $business, $formattedTime, $hostName)
    {
        $this->vendor = $vendor;
        $this->business = $business;
        $this->formattedTime = $formattedTime;
        $this->hostName = $hostName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Booking Received',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor_booking'
        );
    }
}
