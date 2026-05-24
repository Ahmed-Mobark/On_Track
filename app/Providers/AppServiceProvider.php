<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $view->with('siteSettings', [
                'phone' => SiteSetting::get('phone', ''),
                'email' => SiteSetting::get('email', 'info@ontrack.eg'),
                'whatsapp' => SiteSetting::get('whatsapp', '201010300353'),
                'address' => SiteSetting::get('address', ''),
                'facebook_url' => SiteSetting::get('facebook_url', ''),
                'tiktok_url' => SiteSetting::get('tiktok_url', ''),
                'instagram_url' => SiteSetting::get('instagram_url', ''),
            ]);
        });
    }
}
