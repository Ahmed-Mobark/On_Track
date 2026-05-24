<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

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
        $order->update(['status' => $request->status]);
        return back()->with('success', 'تم تحديث حالة الطلب');
    }

    public function updateShipping(Request $request, Order $order)
    {
        $request->validate([
            'shipping_company' => 'required|string',
            'tracking_number' => 'required|string',
            'shipment_status' => 'required|string',
        ]);

        $order->update([
            'shipping_company' => $request->shipping_company,
            'tracking_number' => $request->tracking_number,
            'shipment_status' => $request->shipment_status,
            'status' => 'SHIPPED',
        ]);

        return back()->with('success', 'تم تحديث بيانات الشحن');
    }
}
