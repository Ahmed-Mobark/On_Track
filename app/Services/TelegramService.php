<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected string $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token', '');
        $this->chatId = config('services.telegram.chat_id', '');
    }

    public function send(string $message): bool
    {
        if (!$this->botToken || !$this->chatId) {
            Log::warning('Telegram not configured: missing bot_token or chat_id');
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram notification failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendNewOrderNotification($order): bool
    {
        $items = $order->items->map(function ($item) {
            $name = $item->product->name_ar ?? $item->product->name ?? 'منتج';
            return "  - {$name} ({$item->variant->size}/{$item->variant->color}) x{$item->quantity}";
        })->implode("\n");

        $customerName = $order->user
            ? ($order->user->first_name . ' ' . $order->user->last_name)
            : 'زائر';

        $address = $order->address;
        $location = $address ? "{$address->city}, {$address->governorate}" : '-';

        $paymentLabels = [
            'COD' => 'عند الاستلام',
            'VISA' => 'فيزا',
            'INSTAPAY' => 'انستاباي',
            'WALLET' => 'محفظة',
        ];
        $payment = $paymentLabels[$order->payment_method] ?? $order->payment_method;
        $paymentType = $order->payment_type === 'SHIPPING_ONLY' ? '(شحن فقط)' : ($order->payment_type === 'FULL' ? '(دفع كامل)' : '');

        $message = "🛒 <b>طلب جديد #{$order->order_number}</b>\n\n"
            . "👤 العميل: {$customerName}\n"
            . "📍 الموقع: {$location}\n"
            . "💳 الدفع: {$payment} {$paymentType}\n"
            . ($order->payment_proof ? "📎 إثبات دفع مرفق\n" : "") . "\n"
            . "📦 المنتجات:\n{$items}\n\n"
            . "💰 المجموع: " . number_format($order->total) . " ج.م\n"
            . "🚚 الشحن: " . number_format($order->shipping_cost) . " ج.م";

        if ($order->notes) {
            $message .= "\n📝 ملاحظات: {$order->notes}";
        }

        return $this->send($message);
    }
}
