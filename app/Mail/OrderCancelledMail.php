<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order #{$this->order->id} Cancelled",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-cancelled',
            with: [
                'order' => $this->order,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
