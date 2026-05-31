<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $items = [];
        $subtotal = 0;

        foreach ($cart as $key => $cartItem) {
            $product = Product::with(['images' => fn($q) => $q->orderBy('sort_order')->limit(1)])->find($cartItem['product_id']);
            $variant = ProductVariant::find($cartItem['variant_id']);

            if (!$product || !$variant) {
                unset($cart[$key]);
                session(['cart' => $cart]);
                continue;
            }

            $price = (float) ($variant->price ?? $product->base_price);
            $subtotal += $price * $cartItem['quantity'];

            $items[] = [
                'key' => $key,
                'product' => $product,
                'variant' => $variant,
                'quantity' => $cartItem['quantity'],
                'price' => $price,
            ];
        }

        $freeShippingThreshold = (float) \App\Models\SiteSetting::get('free_shipping_threshold', 2000);
        $shippingCost = ($freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold) ? 0 : null;
        $total = $subtotal + ($shippingCost ?? 0);

        return view('shop.cart', compact('items', 'subtotal', 'shippingCost', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);

        $cart = session('cart', []);
        $key = $request->product_id . '_' . $request->variant_id;
        $currentQty = isset($cart[$key]) ? $cart[$key]['quantity'] : 0;
        $newTotal = $currentQty + $request->quantity;

        if ($variant->quantity < $newTotal) {
            $available = $variant->quantity - $currentQty;
            return back()->withErrors(['quantity' => $available > 0 ? "متبقي {$available} قطع فقط" : 'المخزون غير كافي']);
        }

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $newTotal;
        } else {
            $cart[$key] = [
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
            ];
        }

        session(['cart' => $cart]);
        return back()->with('success', 'تم إضافة المنتج للسلة');
    }

    public function update(Request $request, string $key)
    {
        $cart = session('cart', []);

        if (!isset($cart[$key])) {
            return back();
        }

        if ($request->quantity <= 0) {
            unset($cart[$key]);
        } else {
            $cart[$key]['quantity'] = (int) $request->quantity;
        }

        session(['cart' => $cart]);
        return back()->with('success', 'تم تحديث السلة');
    }

    public function remove(string $key)
    {
        $cart = session('cart', []);
        unset($cart[$key]);
        session(['cart' => $cart]);
        return back()->with('success', 'تم حذف المنتج من السلة');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'تم تفريغ السلة');
    }
}
