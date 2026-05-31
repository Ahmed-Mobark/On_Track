<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
{
    public function checkout()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->withErrors(['cart' => 'السلة فارغة']);
        }

        // Build cart items for display
        $items = [];
        $subtotal = 0;
        foreach ($cart as $cartItem) {
            $product = Product::find($cartItem['product_id']);
            $variant = ProductVariant::find($cartItem['variant_id']);
            if (!$product || !$variant) continue;

            $price = (float) ($variant->price ?? $product->base_price);
            $subtotal += $price * $cartItem['quantity'];
            $items[] = [
                'product' => $product,
                'variant' => $variant,
                'quantity' => $cartItem['quantity'],
                'price' => $price,
            ];
        }

        // Default shipping — will be recalculated when user picks governorate
        $shippingCost = 0;
        $shippingDetermined = false; // whether we know the shipping cost
        $addresses = auth()->check() ? auth()->user()->addresses : collect();
        $isGuest = !auth()->check();

        // If logged in with a default address, use its shipping cost
        $freeShippingThreshold = (float) \App\Models\SiteSetting::get('free_shipping_threshold', 2000);
        $isFreeShipping = ($freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold);

        if (!$isGuest && $addresses->count()) {
            $defaultAddr = $addresses->first();
            $shippingCost = \App\Models\ShippingRate::getCost($defaultAddr->governorate, $defaultAddr->city);
            if ($isFreeShipping) $shippingCost = 0;
            $shippingDetermined = true;
        } elseif ($isFreeShipping) {
            $shippingDetermined = true;
        }

        $total = $subtotal + $shippingCost;

        // Calculate deposit when shipping is free
        $depositMin = (float) \App\Models\SiteSetting::get('deposit_min', 100);
        $depositPercentage = (float) \App\Models\SiteSetting::get('deposit_percentage', 10);
        $depositAmount = $isFreeShipping
            ? max($depositMin, ceil($subtotal * ($depositPercentage / 100)))
            : $depositMin;

        $walletBalance = (!$isGuest) ? (float) (auth()->user()->wallet?->balance ?? 0) : 0;

        return view('shop.checkout', compact('items', 'addresses', 'subtotal', 'shippingCost', 'shippingDetermined', 'isFreeShipping', 'total', 'isGuest', 'depositAmount', 'walletBalance'));
    }

    public function store(Request $request)
    {
        $rules = [
            'payment_method' => 'required|in:COD,VISA,INSTAPAY,WALLET',
            'payment_type' => 'required|in:SHIPPING_ONLY,FULL',
            'payment_proof' => 'required_without:use_wallet|image|mimes:jpg,jpeg,png,webp|max:5120',
            'use_wallet' => 'nullable|numeric|min:0',
        ];

        // Guest checkout: require address info inline
        if (!auth()->check()) {
            $rules += [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string',
                'email' => 'required|email',
                'address' => 'required|string',
                'city' => 'required|string',
                'governorate' => 'required|string',
            ];
        } else {
            $rules['address_id'] = 'required|exists:addresses,id';
        }

        $request->validate($rules);

        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->withErrors(['cart' => 'السلة فارغة']);
        }

        // Build items, validate stock, and calculate totals
        $subtotal = 0;
        $orderItems = [];
        $variants = [];
        $stockErrors = [];

        foreach ($cart as $key => $cartItem) {
            $product = Product::find($cartItem['product_id']);
            $variant = ProductVariant::find($cartItem['variant_id']);
            if (!$product || !$variant) {
                unset($cart[$key]);
                session(['cart' => $cart]);
                continue;
            }

            // Check stock at checkout time
            if ($variant->quantity < $cartItem['quantity']) {
                $name = $product->name_ar ?? $product->name;
                if ($variant->quantity <= 0) {
                    $stockErrors[] = "{$name} ({$variant->color}/{$variant->size}) نفذ من المخزون";
                } else {
                    $stockErrors[] = "{$name} ({$variant->color}/{$variant->size}) متبقي {$variant->quantity} فقط";
                }
                continue;
            }

            $price = (float) ($variant->price ?? $product->base_price);
            $subtotal += $price * $cartItem['quantity'];
            $orderItems[] = [
                'product_id' => $cartItem['product_id'],
                'variant_id' => $cartItem['variant_id'],
                'quantity' => $cartItem['quantity'],
                'price' => $price,
            ];
            $variants[] = ['id' => $variant->id, 'quantity' => $cartItem['quantity']];
        }

        if (!empty($stockErrors)) {
            return back()->withErrors(['stock' => implode('، ', $stockErrors)])->withInput();
        }

        if (empty($orderItems)) {
            return back()->withErrors(['cart' => 'السلة فارغة أو المنتجات غير متاحة']);
        }

        // Coupon
        $discount = 0;
        $couponId = null;
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $couponId = $coupon->id;
                $discount = $coupon->calculateDiscount($subtotal);
                $coupon->increment('used_count');
            }
        }

        // Calculate shipping based on city/governorate
        $governorate = $request->governorate ?? '';
        $city = $request->city ?? '';
        if (!$governorate && auth()->check() && $request->address_id) {
            $addr = Address::find($request->address_id);
            if ($addr) {
                $governorate = $addr->governorate;
                $city = $addr->city;
            }
        }
        $shippingCost = \App\Models\ShippingRate::getCost($governorate, $city);
        $freeShippingThreshold = (float) \App\Models\SiteSetting::get('free_shipping_threshold', 2000);
        if ($freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold) {
            $shippingCost = 0;
        }
        $total = $subtotal - $discount + $shippingCost;

        // Handle guest user + address
        if (!auth()->check()) {
            // Create or find guest user
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                $user = User::create([
                    'email' => $request->email,
                    'password' => Hash::make(uniqid()),
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'role' => 'CUSTOMER',
                ]);
            }

            $address = Address::create([
                'user_id' => $user->id,
                'title' => 'عنوان التوصيل',
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'governorate' => $request->governorate,
                'postal_code' => $request->postal_code,
                'is_default' => true,
            ]);

            $userId = $user->id;
            $addressId = $address->id;
        } else {
            $userId = auth()->id();
            $addressId = $request->address_id;
        }

        // Handle payment proof upload
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        // Calculate deposit amount for partial payments
        $depositAmount = null;
        if ($request->payment_type === 'SHIPPING_ONLY') {
            if ($shippingCost > 0) {
                $depositAmount = $shippingCost;
            } else {
                $depositMin = (float) \App\Models\SiteSetting::get('deposit_min', 100);
                $depositPercentage = (float) \App\Models\SiteSetting::get('deposit_percentage', 10);
                $depositAmount = max($depositMin, ceil(($subtotal - $discount) * ($depositPercentage / 100)));
            }
        } elseif ($request->payment_type === 'FULL') {
            $depositAmount = $total;
        }

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $userId,
            'address_id' => $addressId,
            'payment_method' => 'INSTAPAY',
            'payment_type' => $request->payment_type,
            'payment_status' => 'PENDING',
            'payment_proof' => $paymentProofPath,
            'deposit_amount' => $depositAmount,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'discount' => $discount,
            'total' => $total,
            'coupon_id' => $couponId,
            'notes' => $request->notes,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        // Stock will be decremented when order is CONFIRMED from dashboard

        // Deduct wallet balance if used
        if (($request->use_wallet ?? 0) > 0 && auth()->check()) {
            $wallet = auth()->user()->getOrCreateWallet();
            $walletUsed = min((float) $request->use_wallet, (float) $wallet->balance, $total);
            if ($walletUsed > 0) {
                $wallet->deductBalance($walletUsed, "دفع طلب #{$order->order_number}", 'Order', $order->id);
                $order->update(['wallet_used' => $walletUsed]);
            }
        }

        // Clear session cart
        session()->forget('cart');

        // Send Telegram notification
        try {
            $order->load(['items.product', 'items.variant', 'user', 'address']);
            app(TelegramService::class)->sendNewOrderNotification($order);
        } catch (\Exception $e) {
            // Don't block order if notification fails
        }

        return redirect()->route('order.success', $order)->with('success', 'تم تقديم الطلب بنجاح');
    }

    public function success(Order $order)
    {
        $order->load(['items.product', 'items.variant']);
        return view('shop.order-success', compact('order'));
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product.images'])
            ->latest()
            ->paginate(10);
        return view('shop.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if(auth()->check() && $order->user_id !== auth()->id(), 403);
        $order->load(['items.product.images', 'items.variant', 'address']);
        return view('shop.order-detail', compact('order'));
    }

    public function track(Order $order)
    {
        // If order has tracking URL, redirect to it
        if ($order->tracking_number && $order->shipping_company) {
            // Common Egyptian shipping company tracking URLs
            $trackingUrl = match(strtolower($order->shipping_company)) {
                'aramex' => 'https://www.aramex.com/track/results?ShipmentNumber=' . $order->tracking_number,
                'dhl' => 'https://www.dhl.com/eg-ar/home/tracking.html?tracking-id=' . $order->tracking_number,
                'fedex' => 'https://www.fedex.com/fedextrack/?trknbr=' . $order->tracking_number,
                'bosta' => 'https://bosta.co/tracking/' . $order->tracking_number,
                'mylerz' => 'https://mylerz.com/tracking/' . $order->tracking_number,
                'r2s' => 'https://r2slogistics.com/tracking?tracking_number=' . $order->tracking_number,
                default => null,
            };

            if ($trackingUrl) {
                return redirect()->away($trackingUrl);
            }
        }

        // Fallback: show order detail
        return redirect()->route('orders.show', $order)->with('info', 'لم يتم إضافة رابط تتبع بعد');
    }

    public function publicTrack(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'items.variant'])
            ->firstOrFail();

        return view('shop.track', compact('order'));
    }
}
