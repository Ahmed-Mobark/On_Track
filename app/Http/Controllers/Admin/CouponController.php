<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:PERCENTAGE,FIXED_AMOUNT,FREE_SHIPPING',
            'value' => 'required|numeric|min:0',
        ]);

        Coupon::create($request->only([
            'code', 'type', 'value', 'min_order_value', 'max_uses',
            'is_active', 'is_first_order', 'expires_at',
        ]));

        return redirect()->route('admin.coupons.index')->with('success', 'تم إضافة الكوبون');
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:PERCENTAGE,FIXED_AMOUNT,FREE_SHIPPING',
            'value' => 'required|numeric|min:0',
        ]);

        $coupon->update($request->only([
            'code', 'type', 'value', 'min_order_value', 'max_uses',
            'is_active', 'is_first_order', 'expires_at',
        ]));

        return redirect()->route('admin.coupons.index')->with('success', 'تم تحديث الكوبون');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'تم حذف الكوبون');
    }
}
