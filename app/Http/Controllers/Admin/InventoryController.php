<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $lowStock = ProductVariant::where('quantity', '<=', 5)
            ->with('product')
            ->orderBy('quantity')
            ->get();

        $logs = InventoryLog::with(['variant.product'])
            ->latest('created_at')
            ->paginate(50);

        return view('admin.inventory.index', compact('lowStock', 'logs'));
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'action' => 'required|in:NEW_STOCK,MANUAL_ADJUSTMENT,DAMAGED',
            'notes' => 'nullable|string',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);
        $previousQty = $variant->quantity;

        $newQty = $request->action === 'DAMAGED'
            ? $variant->quantity - abs($request->quantity)
            : $variant->quantity + $request->quantity;

        if ($newQty < 0) {
            return back()->withErrors(['quantity' => 'الكمية الناتجة لا يمكن أن تكون سالبة']);
        }

        $variant->update(['quantity' => $newQty]);

        InventoryLog::create([
            'variant_id' => $request->variant_id,
            'action' => $request->action,
            'quantity' => abs($request->quantity),
            'previous_qty' => $previousQty,
            'new_qty' => $newQty,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'تم تعديل المخزون');
    }
}
