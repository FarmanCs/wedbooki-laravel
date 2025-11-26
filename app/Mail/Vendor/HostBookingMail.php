<?php

namespace App\Mail\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HostBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $host;
    public $business;
    public $formattedTime;

    /**
     * Create a new message instance.
     */
    public function __construct($host, $business, $formattedTime)
    {
        $this->host = $host;
        $this->business = $business;
        $this->formattedTime = $formattedTime;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Host Booking Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.host_booking', // Your Blade view
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
