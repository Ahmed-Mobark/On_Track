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

    {{-- Shipping & Payment --}}
    <div class="bg-brand-dark rounded-xl p-6 border border-white/5">
        <h2 class="text-white font-bold mb-4">الشحن والدفع</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">حد الشحن المجاني (ج.م)</label>
                <input type="number" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" placeholder="2000" min="0">
                <p class="text-white/30 text-xs mt-1">الشحن مجاني لو الأوردر وصل للمبلغ ده أو أكتر. اكتب 0 لإلغاء الشحن المجاني.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white/70 text-sm mb-1">رقم InstaPay</label>
                    <input type="text" name="instapay_number" value="{{ old('instapay_number', $settings['instapay_number']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" placeholder="01xxxxxxxxx">
                </div>
                <div>
                    <label class="block text-white/70 text-sm mb-1">اسم حساب InstaPay</label>
                    <input type="text" name="instapay_name" value="{{ old('instapay_name', $settings['instapay_name']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" placeholder="ON TRACK Store">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white/70 text-sm mb-1">الحد الأدنى للعربون (ج.م)</label>
                    <input type="number" name="deposit_min" value="{{ old('deposit_min', $settings['deposit_min']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" placeholder="100" min="0">
                    <p class="text-white/30 text-xs mt-1">لو الشحن مجاني والأوردر أقل من الحد</p>
                </div>
                <div>
                    <label class="block text-white/70 text-sm mb-1">نسبة العربون (%)</label>
                    <input type="number" name="deposit_percentage" value="{{ old('deposit_percentage', $settings['deposit_percentage']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" placeholder="10" min="0" max="100">
                    <p class="text-white/30 text-xs mt-1">لو الشحن مجاني والأوردر >= حد الشحن المجاني</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Loyalty & Points --}}
    <div class="bg-brand-dark rounded-xl p-6 border border-white/5">
        <h2 class="text-white font-bold mb-4">نظام النقاط والمحفظة</h2>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white/70 text-sm mb-1">نقاط لكل جنيه</label>
                    <input type="number" name="points_per_egp" value="{{ old('points_per_egp', $settings['points_per_egp']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" step="0.1" min="0">
                    <p class="text-white/30 text-xs mt-1">كل 1 ج = كام نقطة</p>
                </div>
                <div>
                    <label class="block text-white/70 text-sm mb-1">مضاعف الدفع الكامل</label>
                    <input type="number" name="full_payment_points_multiplier" value="{{ old('full_payment_points_multiplier', $settings['full_payment_points_multiplier']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" step="0.5" min="1">
                    <p class="text-white/30 text-xs mt-1">x2 = ضعف النقاط لو دفع كامل</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white/70 text-sm mb-1">معدل الاستبدال (نقطة = ج)</label>
                    <input type="number" name="points_redemption_rate" value="{{ old('points_redemption_rate', $settings['points_redemption_rate']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" min="1">
                    <p class="text-white/30 text-xs mt-1">كل 10 نقاط = 1 ج خصم (مثلاً)</p>
                </div>
                <div>
                    <label class="block text-white/70 text-sm mb-1">الحد الأدنى للاستبدال</label>
                    <input type="number" name="min_points_to_redeem" value="{{ old('min_points_to_redeem', $settings['min_points_to_redeem']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" min="0">
                    <p class="text-white/30 text-xs mt-1">أقل عدد نقاط يقدر يستبدلهم</p>
                </div>
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">مكافأة التسجيل (نقاط)</label>
                <input type="number" name="signup_bonus_points" value="{{ old('signup_bonus_points', $settings['signup_bonus_points']) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr" min="0">
                <p class="text-white/30 text-xs mt-1">نقاط ترحيبية عند التسجيل. اكتب 0 لإلغائها.</p>
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
