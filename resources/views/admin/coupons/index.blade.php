@extends('layouts.admin')
@section('title', 'الكوبونات')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">الكوبونات</h1>

<div class="grid md:grid-cols-2 gap-6">
    {{-- Add Form --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-4">إضافة كوبون</h2>
        <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-3">
            @csrf
            <input type="text" name="code" placeholder="كود الكوبون" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red" dir="ltr">
            <select name="type" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none">
                <option value="PERCENTAGE">نسبة مئوية</option>
                <option value="FIXED_AMOUNT">مبلغ ثابت</option>
                <option value="FREE_SHIPPING">شحن مجاني</option>
            </select>
            <input type="number" name="value" placeholder="القيمة" step="0.01" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red" dir="ltr">
            <input type="number" name="min_order_value" placeholder="الحد الأدنى للطلب (اختياري)" step="0.01" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red" dir="ltr">
            <input type="number" name="max_uses" placeholder="الحد الأقصى للاستخدام (اختياري)" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red" dir="ltr">
            <input type="datetime-local" name="expires_at" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            <label class="flex items-center gap-2 text-white/70 text-sm">
                <input type="checkbox" name="is_active" value="1" checked> نشط
            </label>
            <button type="submit" class="bg-brand-red hover:bg-brand-red-dark text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">إضافة</button>
        </form>
    </div>

    {{-- List --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-4">الكوبونات الحالية</h2>
        <div class="space-y-3">
            @foreach($coupons as $coupon)
                <div class="flex items-center justify-between py-3 border-b border-white/5">
                    <div>
                        <p class="text-white font-mono text-sm font-bold">{{ $coupon->code }}</p>
                        <p class="text-white/40 text-xs">
                            {{ $coupon->type === 'PERCENTAGE' ? $coupon->value . '%' : ($coupon->type === 'FIXED_AMOUNT' ? $coupon->value . ' ج.م' : 'شحن مجاني') }}
                            · استخدم {{ $coupon->used_count }} مرة
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs {{ $coupon->is_active ? 'text-green-400' : 'text-red-400' }}">{{ $coupon->is_active ? 'نشط' : 'معطل' }}</span>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('حذف؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 text-xs hover:underline">حذف</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
