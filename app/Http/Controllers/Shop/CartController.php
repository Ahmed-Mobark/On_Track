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

        $shippingCost = $subtotal > 500 ? 0 : 50;
        $total = $subtotal + $shippingCost;

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
        if ($variant->quantity < $request->quantity) {
            return back()->withErrors(['quantity' => 'المخزون غير كافي']);
        }

        $cart = session('cart', []);
        $key = $request->product_id . '_' . $request->variant_id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
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
