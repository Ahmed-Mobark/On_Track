<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BostaWebhookController extends Controller
{
    /**
     * Bosta status mapping to our shipment_status
     */
    private const STATUS_MAP = [
        'TICKET_CREATED' => 'AWAITING_PICKUP',
        'PACKAGE_RECEIVED' => 'PICKED_UP',
        'IN_TRANSIT' => 'IN_TRANSIT',
        'OUT_FOR_DELIVERY' => 'OUT_FOR_DELIVERY',
        'DELIVERED' => 'DELIVERED',
        'RETURNED_TO_BUSINESS' => 'RETURNED',
        'EXCEPTION' => 'DELIVERY_FAILED',
        'TERMINATED' => 'RETURNED',
        'CANCELLED' => 'RETURNED',
    ];

    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Bosta webhook received', $payload);

        $trackingNumber = $payload['TrackingNumber'] ?? $payload['trackingNumber'] ?? null;
        $bostaStatus = $payload['CurrentStatus']['state'] ?? $payload['state'] ?? null;

        if (!$trackingNumber || !$bostaStatus) {
            return response()->json(['message' => 'Missing data'], 400);
        }

        $order = Order::where('tracking_number', $trackingNumber)->first();
        if (!$order) {
            Log::warning("Bosta webhook: order not found for tracking {$trackingNumber}");
            return response()->json(['message' => 'Order not found'], 404);
        }

        $newShipmentStatus = self::STATUS_MAP[$bostaStatus] ?? null;
        if (!$newShipmentStatus) {
            Log::info("Bosta webhook: unmapped status {$bostaStatus}");
            return response()->json(['message' => 'Status noted']);
        }

        $updates = ['shipment_status' => $newShipmentStatus];

        // Auto-update order status based on shipment
        if ($newShipmentStatus === 'DELIVERED') {
            $updates['status'] = 'DELIVERED';
            if ($order->payment_type === 'SHIPPING_ONLY') {
                $updates['payment_status'] = 'PAID'; // COD collected
            }
        } elseif (in_array($newShipmentStatus, ['RETURNED', 'DELIVERY_FAILED'])) {
            if ($newShipmentStatus === 'RETURNED') {
                $updates['status'] = 'RETURNED';
            }
        } elseif ($newShipmentStatus === 'OUT_FOR_DELIVERY') {
            $updates['status'] = 'SHIPPED';
        }

        $order->update($updates);

        // Notify admin via Telegram
        $this->notifyStatusChange($order, $bostaStatus, $newShipmentStatus);

        return response()->json(['message' => 'OK']);
    }

    private function notifyStatusChange(Order $order, string $bostaStatus, string $shipmentStatus): void
    {
        try {
            $statusLabels = [
                'AWAITING_PICKUP' => 'في انتظار الاستلام',
                'PICKED_UP' => 'تم الاستلام من المخزن',
                'IN_TRANSIT' => 'في الطريق',
                'OUT_FOR_DELIVERY' => 'خارج للتوصيل',
                'DELIVERED' => 'تم التوصيل',
                'DELIVERY_FAILED' => 'فشل التوصيل',
                'RETURNED' => 'مرتجع',
            ];

            $label = $statusLabels[$shipmentStatus] ?? $bostaStatus;
            $customer = $order->user ? ($order->user->first_name . ' ' . $order->user->last_name) : '';

            $msg = "<b>تحديث شحن #{$order->order_number}</b>\n"
                . "الحالة: {$label}\n"
                . "العميل: {$customer}\n"
                . "الإجمالي: " . number_format($order->total) . " ج.م";

            if ($shipmentStatus === 'DELIVERED') {
                $msg .= "\n✅ تم التوصيل بنجاح!";
            } elseif ($shipmentStatus === 'DELIVERY_FAILED') {
                $msg .= "\n❌ فشل التوصيل - تابع مع بوسطة";
            } elseif ($shipmentStatus === 'RETURNED') {
                $msg .= "\n🔄 الشحنة مرتجعة";
            }

            app(TelegramService::class)->send($msg);
        } catch (\Exception $e) {
            // Don't block webhook
        }
    }
}
