@extends('layouts.admin')
@section('title', 'طلب #' . $order->order_number)

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">طلب #{{ $order->order_number }}</h1>

<div class="grid md:grid-cols-4 gap-4 mb-6">
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">العميل</p>
        <p class="text-white font-medium">{{ $order->user->name ?? '-' }}</p>
        <p class="text-white/40 text-xs">{{ $order->user->email ?? '' }}</p>
        <p class="text-white/40 text-xs">{{ $order->user->phone ?? '' }}</p>
    </div>
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">الإجمالي</p>
        <p class="text-brand-red font-bold text-lg">{{ number_format($order->total) }} ج.م</p>
        <p class="text-white/30 text-xs">شحن: {{ number_format($order->shipping_cost) }} ج.م</p>
    </div>
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">الدفع</p>
        <p class="text-white font-medium">{{ $order->payment_method }}</p>
        <p class="text-xs {{ $order->payment_status === 'PAID' ? 'text-green-400' : 'text-yellow-400' }}">{{ $order->payment_status }}</p>
    </div>
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">التاريخ</p>
        <p class="text-white">{{ $order->created_at->format('Y/m/d H:i') }}</p>
    </div>
</div>

{{-- Address --}}
@if($order->address)
<div class="bg-brand-dark rounded-xl p-6 mb-6 border border-white/5">
    <h2 class="text-white font-bold mb-3">عنوان التوصيل</h2>
    <p class="text-white/70 text-sm">{{ $order->address->first_name }} {{ $order->address->last_name }}</p>
    <p class="text-white/50 text-sm">{{ $order->address->address }}</p>
    <p class="text-white/50 text-sm">{{ $order->address->city }}، {{ $order->address->governorate }}</p>
    <p class="text-white/50 text-sm">{{ $order->address->phone }}</p>
</div>
@endif

<div class="grid md:grid-cols-2 gap-6 mb-6">
    {{-- Update Status --}}
    <div class="bg-brand-dark rounded-xl p-6 border border-white/5">
        <h2 class="text-white font-bold mb-3">تحديث الحالة</h2>
        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex items-center gap-3">
            @csrf @method('PATCH')
            <select name="status" style="flex:1;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px;color:white;font-size:13px;">
                @foreach(['PENDING' => 'معلق', 'CONFIRMED' => 'مؤكد', 'PROCESSING' => 'قيد التجهيز', 'SHIPPED' => 'تم الشحن', 'DELIVERED' => 'تم التوصيل', 'CANCELLED' => 'ملغي', 'RETURNED' => 'مرتجع'] as $val => $label)
                    <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }} style="background:#141414;">{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" style="background:#e63946;color:white;padding:10px 20px;border-radius:8px;border:none;cursor:pointer;font-size:13px;font-weight:600;">تحديث</button>
        </form>
    </div>

    {{-- Shipping / Tracking --}}
    <div class="bg-brand-dark rounded-xl p-6 border border-white/5">
        <h2 class="text-white font-bold mb-3">بيانات الشحن والتتبع</h2>
        <form action="{{ route('admin.orders.shipping', $order) }}" method="POST" class="space-y-3">
            @csrf @method('PATCH')
            <select name="shipping_company" style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px;color:white;font-size:13px;">
                <option value="" style="background:#141414;">اختر شركة الشحن</option>
                @foreach(['Bosta' => 'بوسطة', 'Mylerz' => 'مايلرز', 'R2S' => 'R2S', 'Aramex' => 'أرامكس', 'DHL' => 'DHL', 'FedEx' => 'فيديكس'] as $val => $label)
                    <option value="{{ $val }}" {{ $order->shipping_company === $val ? 'selected' : '' }} style="background:#141414;">{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="رقم التتبع"
                style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px;color:white;font-size:13px;" dir="ltr">
            <select name="shipment_status" style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px;color:white;font-size:13px;">
                @foreach(['AWAITING_PICKUP' => 'في انتظار الاستلام', 'PICKED_UP' => 'تم الاستلام', 'IN_TRANSIT' => 'في الطريق', 'OUT_FOR_DELIVERY' => 'خارج للتوصيل', 'DELIVERED' => 'تم التوصيل', 'DELIVERY_FAILED' => 'فشل التوصيل', 'RETURNED' => 'مرتجع'] as $val => $label)
                    <option value="{{ $val }}" {{ $order->shipment_status === $val ? 'selected' : '' }} style="background:#141414;">{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" style="width:100%;background:#e63946;color:white;padding:10px;border-radius:8px;border:none;cursor:pointer;font-size:13px;font-weight:600;">حفظ بيانات الشحن</button>
        </form>
        @if($order->tracking_number)
            <div style="margin-top:12px;padding:12px;background:rgba(255,255,255,0.03);border-radius:8px;">
                <p class="text-white/40 text-xs mb-1">رابط التتبع للعميل:</p>
                <a href="{{ route('order.track', $order) }}" target="_blank" style="color:#e63946;font-size:12px;word-break:break-all;">
                    {{ url('/order/' . $order->id . '/track') }}
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Tracking Link + WhatsApp --}}
<div class="bg-brand-dark rounded-xl p-6 mb-6 border border-white/5">
    <h2 class="text-white font-bold mb-3">تواصل مع العميل</h2>

    @php $trackUrl = url('/track/' . $order->order_number); @endphp
    <div class="mb-4">
        <p class="text-white/40 text-xs mb-1">رابط تتبع الطلب (عام — بدون تسجيل دخول)</p>
        <div class="flex items-center gap-2">
            <input type="text" value="{{ $trackUrl }}" readonly dir="ltr" id="track-url"
                style="flex:1;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:8px 12px;color:white;font-size:12px;">
            <button type="button" onclick="navigator.clipboard.writeText('{{ $trackUrl }}');this.textContent='تم!';setTimeout(()=>this.textContent='نسخ',1500)"
                style="background:rgba(255,255,255,0.1);color:white;padding:8px 16px;border-radius:8px;border:none;cursor:pointer;font-size:12px;">نسخ</button>
            <a href="{{ $trackUrl }}" target="_blank"
                style="background:rgba(255,255,255,0.1);color:white;padding:8px 16px;border-radius:8px;text-decoration:none;font-size:12px;">فتح</a>
        </div>
    </div>

    @php
        $phone = $order->user?->phone ?? $order->address?->phone ?? '';
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) $phone = '2' . $phone;
        if (!str_starts_with($phone, '20')) $phone = '20' . $phone;

        $customerName = $order->user?->first_name ?? 'عميلنا';
        $sLabels = ['PENDING'=>'تم استلام','CONFIRMED'=>'تم تأكيد','PROCESSING'=>'جاري تجهيز','SHIPPED'=>'تم شحن','DELIVERED'=>'تم توصيل','CANCELLED'=>'تم إلغاء'];
        $sText = $sLabels[$order->status] ?? 'تحديث على';
        $waMsg = "مرحباً {$customerName}\n{$sText} طلبك #{$order->order_number}\n\nتتبع الطلب: {$trackUrl}";
        if ($order->tracking_number) $waMsg .= "\nرقم التتبع: {$order->tracking_number}";
    @endphp

    @if($phone)
        <div class="flex gap-2">
            <a href="https://wa.me/{{ $phone }}?text={{ urlencode($waMsg) }}" target="_blank"
                style="flex:1;display:flex;align-items:center;justify-content:center;gap:8px;background:#22c55e;color:white;font-weight:700;padding:12px;border-radius:10px;font-size:14px;text-decoration:none;">
                <svg style="width:18px;height:18px;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                ابعت للعميل على واتساب
            </a>
            <a href="tel:+{{ $phone }}"
                style="display:flex;align-items:center;justify-content:center;gap:8px;background:rgba(255,255,255,0.1);color:white;padding:12px 20px;border-radius:10px;font-size:14px;text-decoration:none;">
                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                اتصل
            </a>
        </div>
    @else
        <p class="text-white/30 text-sm">لا يوجد رقم هاتف للعميل</p>
    @endif
</div>

{{-- Items --}}
<div class="bg-brand-dark rounded-xl p-6 border border-white/5">
    <h2 class="text-white font-bold mb-3">المنتجات</h2>
    @foreach($order->items as $item)
        <div class="flex items-center gap-4 py-3 border-b border-white/5 last:border-0">
            <div class="w-12 h-12 bg-white/5 rounded-lg overflow-hidden">
                @if($item->product->images->first())
                    <img src="{{ $item->product->images->first()->image_url }}" class="w-full h-full object-cover">
                @endif
            </div>
            <div class="flex-1">
                <p class="text-white text-sm">{{ $item->product->name }}</p>
                <p class="text-white/40 text-xs">{{ $item->variant->size }}/{{ $item->variant->color }} × {{ $item->quantity }}</p>
            </div>
            <span class="text-white font-medium text-sm">{{ number_format($item->price * $item->quantity) }} ج.م</span>
        </div>
    @endforeach

    {{-- Summary --}}
    <div class="mt-4 pt-4 border-t border-white/10 space-y-2 text-sm">
        <div class="flex justify-between text-white/50"><span>المجموع الفرعي</span><span>{{ number_format($order->subtotal) }} ج.م</span></div>
        <div class="flex justify-between text-white/50"><span>الشحن</span><span>{{ number_format($order->shipping_cost) }} ج.م</span></div>
        @if($order->discount > 0)
            <div class="flex justify-between text-green-400"><span>الخصم</span><span>-{{ number_format($order->discount) }} ج.م</span></div>
        @endif
        <div class="flex justify-between text-white font-bold text-lg pt-2 border-t border-white/10">
            <span>الإجمالي</span>
            <span style="color:#e63946;">{{ number_format($order->total) }} ج.م</span>
        </div>
    </div>
</div>
@endsection
