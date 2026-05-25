<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusMail;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('first_name', 'like', "%{$request->search}%"));
            });
        }

        $orders = $query->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'address', 'items.product.images', 'items.variant', 'coupon']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:PENDING,CONFIRMED,PROCESSING,SHIPPED,DELIVERED,CANCELLED,RETURNED']);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Decrement stock + create Bosta delivery when confirming order
        if ($oldStatus === 'PENDING' && in_array($newStatus, ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED'])) {
            foreach ($order->items as $item) {
                ProductVariant::where('id', $item->variant_id)->decrement('quantity', $item->quantity);
            }

            // Auto-create Bosta delivery
            try {
                $bostaResult = app(\App\Services\BostaService::class)->createDelivery($order);
                if ($bostaResult['success']) {
                    $order->refresh(); // reload tracking_number set by BostaService
                }
            } catch (\Exception $e) {
                // Don't block order confirmation if Bosta fails
            }
        }

        // Restore stock when cancelling/returning a confirmed order
        if (in_array($oldStatus, ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED']) && in_array($newStatus, ['CANCELLED', 'RETURNED'])) {
            foreach ($order->items as $item) {
                ProductVariant::where('id', $item->variant_id)->increment('quantity', $item->quantity);
            }
        }

        $order->update(['status' => $newStatus]);

        // Send email notification to customer
        $this->notifyCustomer($order, $newStatus);

        return back()->with('success', 'تم تحديث حالة الطلب وإرسال إشعار للعميل');
    }

    public function updateShipping(Request $request, Order $order)
    {
        $request->validate([
            'shipping_company' => 'required|string',
            'tracking_number' => 'required|string',
            'shipment_status' => 'required|string',
        ]);

        $oldStatus = $order->status;

        if ($oldStatus === 'PENDING') {
            foreach ($order->items as $item) {
                ProductVariant::where('id', $item->variant_id)->decrement('quantity', $item->quantity);
            }
        }

        $order->update([
            'shipping_company' => $request->shipping_company,
            'tracking_number' => $request->tracking_number,
            'shipment_status' => $request->shipment_status,
            'status' => 'SHIPPED',
        ]);

        $this->notifyCustomer($order, 'SHIPPED');

        return back()->with('success', 'تم تحديث بيانات الشحن وإرسال إشعار للعميل');
    }

    private function notifyCustomer(Order $order, string $newStatus): void
    {
        $order->load(['user', 'items.product', 'items.variant']);

        // Send email
        $email = $order->user?->email;
        if ($email) {
            try {
                Mail::to($email)->queue(new OrderStatusMail($order, $newStatus));
            } catch (\Exception $e) {
                // Don't block status update if email fails
            }
        }

        // Send Telegram notification to admin
        try {
            $statusLabels = [
                'CONFIRMED' => 'مؤكد', 'PROCESSING' => 'قيد التجهيز',
                'SHIPPED' => 'تم الشحن', 'DELIVERED' => 'تم التوصيل',
                'CANCELLED' => 'ملغي', 'RETURNED' => 'مرتجع',
            ];
            $label = $statusLabels[$newStatus] ?? $newStatus;
            $trackUrl = url('/track/' . $order->order_number);

            $msg = "<b>تحديث طلب #{$order->order_number}</b>\n"
                . "الحالة: {$label}\n"
                . "العميل: {$order->user?->first_name} {$order->user?->last_name}\n"
                . "رابط التتبع: {$trackUrl}";

            app(\App\Services\TelegramService::class)->send($msg);
        } catch (\Exception $e) {
            //
        }
    }
}
