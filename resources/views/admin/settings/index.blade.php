@extends('layouts.admin')
@section('title', 'الإعدادات')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">الإعدادات</h1>

<form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6 max-w-2xl">
    @csrf
    @method('PUT')

    {{-- Store Info --}}
    <div class="bg-brand-dark rounded-xl p-6 border border-white/5">
        <h2 class="text-white font-bold mb-4">معلومات المتجر</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">اسم المتجر</label>
                <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email', $settings['email']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" placeholder="info@ontrack.eg">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">رقم الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone', $settings['phone']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" placeholder="01xxxxxxxxx">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">رقم الواتساب</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $settings['whatsapp']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" placeholder="201010300353">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">العنوان</label>
                <input type="text" name="address" value="{{ old('address', $settings['address']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" placeholder="القاهرة، مصر">
            </div>
        </div>
    </div>

    {{-- Social Media --}}
    <div class="bg-brand-dark rounded-xl p-6 border border-white/5">
        <h2 class="text-white font-bold mb-4">السوشيال ميديا</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">رابط الفيسبوك</label>
                <input type="url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" placeholder="https://facebook.com/ontrack">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">رابط تيك توك</label>
                <input type="url" name="tiktok_url" value="{{ old('tiktok_url', $settings['tiktok_url']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" placeholder="https://tiktok.com/@ontrack">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">رابط انستجرام</label>
                <input type="url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" placeholder="https://instagram.com/ontrack">
            </div>
        </div>
    </div>

    <button type="submit" class="bg-brand-red hover:bg-brand-red-dark text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
        حفظ الإعدادات
    </button>
</form>
@endsection
