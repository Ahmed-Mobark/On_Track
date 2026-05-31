<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\SiteSetting;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $addresses = $user->addresses;
        $ordersCount = $user->orders()->count();
        $wallet = $user->getOrCreateWallet();
        $transactions = $wallet->transactions()->take(10)->get();
        $pointsValue = Wallet::pointsToEgp($wallet->points);
        $minRedeem = (int) SiteSetting::get('min_points_to_redeem', 100);
        return view('shop.account', compact('user', 'addresses', 'ordersCount', 'wallet', 'transactions', 'pointsValue', 'minRedeem'));
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

    public function redeemPoints(Request $request)
    {
        $request->validate(['points' => 'required|integer|min:1']);

        $user = auth()->user();
        $wallet = $user->getOrCreateWallet();
        $points = (int) $request->points;
        $minRedeem = (int) SiteSetting::get('min_points_to_redeem', 100);

        if ($points < $minRedeem) {
            return back()->withErrors(['points' => "الحد الأدنى للاستبدال {$minRedeem} نقطة"]);
        }
        if ($wallet->points < $points) {
            return back()->withErrors(['points' => 'رصيد النقاط غير كافي']);
        }

        $discount = Wallet::pointsToEgp($points);
        if ($discount < 1) {
            return back()->withErrors(['points' => 'النقاط غير كافية لإنشاء خصم']);
        }

        // Generate a unique promo code
        $code = 'PTS-' . strtoupper(Str::random(6));
        while (Coupon::where('code', $code)->exists()) {
            $code = 'PTS-' . strtoupper(Str::random(6));
        }

        // Create coupon
        Coupon::create([
            'code' => $code,
            'type' => 'FIXED',
            'value' => $discount,
            'max_uses' => 1,
            'used_count' => 0,
            'expires_at' => now()->addDays(30),
            'is_active' => true,
        ]);

        // Deduct points
        $wallet->deductPoints($points, "استبدال {$points} نقطة بكود خصم {$code}", 'Points', $code);

        return back()->with('success', "تم إنشاء كود خصم: {$code} بقيمة " . number_format($discount) . " ج.م - صالح لمدة 30 يوم");
    }
}
