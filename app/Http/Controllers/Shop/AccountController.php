<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $addresses = $user->addresses;
        $ordersCount = $user->orders()->count();
        return view('shop.account', compact('user', 'addresses', 'ordersCount'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string',
        ]);

        auth()->user()->update($request->only('first_name', 'last_name', 'phone'));
        return back()->with('success', 'تم تحديث البيانات');
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'governorate' => 'required|string',
        ]);

        $address = auth()->user()->addresses()->create($request->all());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'id' => $address->id,
                'title' => $address->title,
                'address' => $address->address,
                'city' => $address->city,
                'governorate' => $address->governorate,
            ]);
        }

        return back()->with('success', 'تم إضافة العنوان');
    }

    public function deleteAddress(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);
        $address->delete();
        return back()->with('success', 'تم حذف العنوان');
    }
}
