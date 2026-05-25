<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $newStatus;
    public string $trackingUrl;

    private const STATUS_LABELS = [
        'CONFIRMED' => 'تم تأكيد طلبك',
        'PROCESSING' => 'طلبك قيد التجهيز',
        'SHIPPED' => 'تم شحن طلبك',
        'DELIVERED' => 'تم توصيل طلبك',
        'CANCELLED' => 'تم إلغاء طلبك',
    ];

    public function __construct(Order $order, string $newStatus)
    {
        $this->order = $order;
        $this->newStatus = $newStatus;
        $this->trackingUrl = url('/track/' . $order->order_number);
    }

    public function envelope(): Envelope
    {
        $label = self::STATUS_LABELS[$this->newStatus] ?? 'تحديث على طلبك';
        return new Envelope(
            subject: "{$label} - #{$this->order->order_number} | On Track",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
