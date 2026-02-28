<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $order, public string $oldStatus, public string $newStatus)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order #{$this->order->id} Status Update: {$this->newStatus}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-changed',
            with: [
                'order' => $this->order,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
