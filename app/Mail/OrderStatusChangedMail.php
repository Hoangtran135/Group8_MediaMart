<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MediaMart - Đơn hàng #' . $this->order->id . ' đã cập nhật: ' . $this->order->status_label,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-status-changed',
            with: ['order' => $this->order],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
