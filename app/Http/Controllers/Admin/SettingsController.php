<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => SiteSetting::get('store_name', 'On Track'),
            'phone' => SiteSetting::get('phone', ''),
            'email' => SiteSetting::get('email', ''),
            'whatsapp' => SiteSetting::get('whatsapp', ''),
            'address' => SiteSetting::get('address', ''),
            'facebook_url' => SiteSetting::get('facebook_url', ''),
            'tiktok_url' => SiteSetting::get('tiktok_url', ''),
            'instagram_url' => SiteSetting::get('instagram_url', ''),
            'free_shipping_threshold' => SiteSetting::get('free_shipping_threshold', '2000'),
            'instapay_number' => SiteSetting::get('instapay_number', ''),
            'instapay_name' => SiteSetting::get('instapay_name', 'ON TRACK Store'),
            'deposit_min' => SiteSetting::get('deposit_min', '100'),
            'deposit_percentage' => SiteSetting::get('deposit_percentage', '10'),
            'points_per_egp' => SiteSetting::get('points_per_egp', '1'),
            'full_payment_points_multiplier' => SiteSetting::get('full_payment_points_multiplier', '2'),
            'points_redemption_rate' => SiteSetting::get('points_redemption_rate', '10'),
            'signup_bonus_points' => SiteSetting::get('signup_bonus_points', '50'),
            'min_points_to_redeem' => SiteSetting::get('min_points_to_redeem', '100'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|url|max:500',
            'tiktok_url' => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:500',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'instapay_number' => 'nullable|string|max:50',
            'instapay_name' => 'nullable|string|max:255',
            'deposit_min' => 'nullable|numeric|min:0',
            'deposit_percentage' => 'nullable|numeric|min:0|max:100',
            'points_per_egp' => 'nullable|numeric|min:0',
            'full_payment_points_multiplier' => 'nullable|numeric|min:1|max:10',
            'points_redemption_rate' => 'nullable|numeric|min:1',
            'signup_bonus_points' => 'nullable|integer|min:0',
            'min_points_to_redeem' => 'nullable|integer|min:0',
        ]);

        SiteSetting::setMany($validated);

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}
