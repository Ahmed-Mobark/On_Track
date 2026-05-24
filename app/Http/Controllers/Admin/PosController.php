<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\InventoryLog;
use App\Models\PosSession;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        $activeSession = PosSession::where('cashier_id', auth()->id())
            ->where('is_open', true)->first();
        return view('admin.pos.index', compact('activeSession'));
    }

    public function openSession(Request $request)
    {
        $request->validate(['opening_cash' => 'required|numeric|min:0']);

        $existing = PosSession::where('cashier_id', auth()->id())->where('is_open', true)->first();
        if ($existing) {
            return back()->withErrors(['error' => 'لديك وردية مفتوحة بالفعل']);
        }

        PosSession::create([
            'cashier_id' => auth()->id(),
            'opening_cash' => $request->opening_cash,
        ]);

        return redirect()->route('admin.pos.index')->with('success', 'تم فتح الوردية');
    }

    public function closeSession(Request $request)
    {
        $request->validate(['closing_cash' => 'required|numeric|min:0']);

        $session = PosSession::where('cashier_id', auth()->id())->where('is_open', true)->first();
        if (!$session) return back()->withErrors(['error' => 'لا يوجد وردية مفتوحة']);

        $expectedCash = $session->opening_cash + $session->total_cash - $session->total_returns;

        $session->update([
            'closing_cash' => $request->closing_cash,
            'expected_cash' => $expectedCash,
            'notes' => $request->notes,
            'is_open' => false,
            'closed_at' => now(),
        ]);

        return redirect()->route('admin.pos.index')->with('success', 'تم إغلاق الوردية');
    }

    public function searchProducts(Request $request)
    {
        $query = $request->q;
        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('name_ar', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhereHas('variants', fn($v) => $v->where('sku', 'like', "%{$query}%"));
            })
            ->with(['images' => fn($q) => $q->orderBy('sort_order')->limit(1), 'variants'])
            ->take(20)
            ->get();

        return response()->json($products);
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:CASH,VISA,INSTAPAY,WALLET',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        $session = PosSession::where('cashier_id', auth()->id())->where('is_open', true)->first();
        if (!$session) return response()->json(['message' => 'لا يوجد وردية مفتوحة'], 400);

        // Validate stock
        foreach ($request->items as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if (!$variant || $variant->quantity < $item['quantity']) {
                return response()->json(['message' => "المخزون غير كافي للمنتج {$variant?->sku}"], 400);
            }
        }

        // Calculate totals
        $subtotal = 0;
        $transactionItems = [];
        foreach ($request->items as $item) {
            $itemDiscount = $item['discount'] ?? 0;
            $itemTotal = ($item['price'] - $itemDiscount) * $item['quantity'];
            $subtotal += $itemTotal;
            $transactionItems[] = [
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => $itemDiscount,
                'total' => $itemTotal,
            ];
        }

        $discount = $request->discount ?? 0;
        $couponId = null;

        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $couponId = $coupon->id;
                $discount += $coupon->calculateDiscount($subtotal);
            }
        }

        $total = $subtotal - $discount;
        $changeAmount = max(0, $request->amount_paid - $total);

        $transaction = PosTransaction::create([
            'transaction_number' => PosTransaction::generateNumber(),
            'session_id' => $session->id,
            'cashier_id' => auth()->id(),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'payment_method' => $request->payment_method,
            'amount_paid' => $request->amount_paid,
            'change_amount' => $changeAmount,
            'coupon_id' => $couponId,
            'notes' => $request->notes,
        ]);

        foreach ($transactionItems as $item) {
            $transaction->items()->create($item);
        }

        // Update inventory
        foreach ($request->items as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            $previousQty = $variant->quantity;

            $variant->decrement('quantity', $item['quantity']);

            InventoryLog::create([
                'variant_id' => $item['variant_id'],
                'action' => 'SALE',
                'quantity' => $item['quantity'],
                'previous_qty' => $previousQty,
                'new_qty' => $previousQty - $item['quantity'],
                'reference' => $transaction->transaction_number,
                'user_id' => auth()->id(),
            ]);

            Product::where('id', $item['product_id'])->increment('total_sold', $item['quantity']);
        }

        // Update session totals
        $paymentField = match ($request->payment_method) {
            'CASH' => 'total_cash',
            'VISA' => 'total_visa',
            'INSTAPAY' => 'total_instapay',
            'WALLET' => 'total_wallet',
            default => 'total_cash',
        };

        $session->increment('total_sales', $total);
        $session->increment('transaction_count');
        $session->increment($paymentField, $total);

        if ($couponId) {
            Coupon::where('id', $couponId)->increment('used_count');
        }

        $transaction->load(['items.product.images', 'items.variant', 'cashier']);

        return response()->json($transaction);
    }

    public function processReturn(Request $request)
    {
        $request->validate([
            'original_transaction_number' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'return_reason' => 'required|string',
        ]);

        $session = PosSession::where('cashier_id', auth()->id())->where('is_open', true)->first();
        if (!$session) return response()->json(['message' => 'لا يوجد وردية مفتوحة'], 400);

        $original = PosTransaction::where('transaction_number', $request->original_transaction_number)
            ->with('items')->first();
        if (!$original) return response()->json(['message' => 'المعاملة الأصلية غير موجودة'], 404);

        $returnSubtotal = 0;
        $returnItems = [];

        foreach ($request->items as $returnItem) {
            $originalItem = $original->items->firstWhere('variant_id', $returnItem['variant_id']);
            if (!$originalItem) {
                return response()->json(['message' => "المنتج غير موجود في المعاملة الأصلية"], 400);
            }
            if ($returnItem['quantity'] > $originalItem->quantity) {
                return response()->json(['message' => "كمية الإرجاع أكبر من الكمية الأصلية"], 400);
            }

            $itemTotal = $originalItem->price * $returnItem['quantity'];
            $returnSubtotal += $itemTotal;
            $returnItems[] = [
                'product_id' => $originalItem->product_id,
                'variant_id' => $returnItem['variant_id'],
                'quantity' => $returnItem['quantity'],
                'price' => $originalItem->price,
                'discount' => 0,
                'total' => $itemTotal,
            ];
        }

        $returnTransaction = PosTransaction::create([
            'transaction_number' => PosTransaction::generateNumber('RET'),
            'session_id' => $session->id,
            'cashier_id' => auth()->id(),
            'customer_name' => $original->customer_name,
            'customer_phone' => $original->customer_phone,
            'subtotal' => $returnSubtotal,
            'total' => $returnSubtotal,
            'payment_method' => $original->payment_method,
            'amount_paid' => $returnSubtotal,
            'status' => 'RETURNED',
            'return_reason' => $request->return_reason,
            'original_transaction_id' => $original->id,
        ]);

        foreach ($returnItems as $item) {
            $returnTransaction->items()->create($item);
        }

        // Restore inventory
        foreach ($request->items as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            $previousQty = $variant->quantity;
            $variant->increment('quantity', $item['quantity']);

            InventoryLog::create([
                'variant_id' => $item['variant_id'],
                'action' => 'RETURN',
                'quantity' => $item['quantity'],
                'previous_qty' => $previousQty,
                'new_qty' => $previousQty + $item['quantity'],
                'reference' => $returnTransaction->transaction_number,
                'user_id' => auth()->id(),
            ]);
        }

        $session->increment('total_returns', $returnSubtotal);

        return response()->json($returnTransaction->load(['items.product', 'items.variant']));
    }

    // Shifts history
    public function shifts()
    {
        $sessions = PosSession::with('cashier')
            ->withCount('transactions')
            ->latest('opened_at')
            ->paginate(20);
        return view('admin.pos.shifts', compact('sessions'));
    }

    // Returns history
    public function returns()
    {
        $returns = PosTransaction::where('status', 'RETURNED')
            ->with(['cashier', 'items.product'])
            ->latest()
            ->paginate(20);
        return view('admin.pos.returns', compact('returns'));
    }
}
