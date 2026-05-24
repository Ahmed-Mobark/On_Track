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
        ]);

        SiteSetting::setMany($validated);

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}
