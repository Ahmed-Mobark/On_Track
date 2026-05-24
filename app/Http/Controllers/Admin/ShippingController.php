<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $rates = ShippingRate::orderBy('governorate')->orderBy('city')->get();

        // Group by governorate
        $grouped = $rates->groupBy('governorate');

        return view('admin.shipping.index', compact('rates', 'grouped'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'governorate' => 'required|string',
            'city' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:1',
        ]);

        ShippingRate::updateOrCreate(
            ['governorate' => $request->governorate, 'city' => $request->city ?: null],
            ['cost' => $request->cost, 'estimated_days' => $request->estimated_days, 'is_active' => true]
        );

        return back()->with('success', 'تم حفظ سعر الشحن');
    }

    public function destroy(ShippingRate $shippingRate)
    {
        $shippingRate->delete();
        return back()->with('success', 'تم حذف سعر الشحن');
    }

    // API endpoint for checkout to get shipping cost
    public function getCost(Request $request)
    {
        $cost = ShippingRate::getCost($request->governorate, $request->city);
        return response()->json(['cost' => $cost]);
    }
}
