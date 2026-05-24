<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $items = WishlistItem::where('user_id', auth()->id())
            ->with(['product.images' => fn($q) => $q->orderBy('sort_order')->limit(1)])
            ->get();
        return view('shop.wishlist', compact('items'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $existing = WishlistItem::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)->first();

        if ($existing) {
            $existing->delete();
            $added = false;
        } else {
            WishlistItem::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
            ]);
            $added = true;
        }

        if ($request->expectsJson()) {
            return response()->json(['added' => $added]);
        }

        return back()->with('success', $added ? 'تم الإضافة للمفضلة' : 'تم الحذف من المفضلة');
    }
}
