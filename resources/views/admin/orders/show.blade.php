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

{{-- Items --}}
<div class="bg-brand-dark rounded-xl p-6 border border-white/5">
    <h2 class="text-white font-bold mb-3">المنتجات</h2>
    @foreach($order->items as $item)
        <div class="flex items-center gap-4 py-3 border-b border-white/5 last:border-0">
            <div class="w-12 h-12 bg-white/5 rounded-lg overflow-hidden">
                @if($item->product->images->first())
                    <img src="{{ $item->product->images->first()->url }}" class="w-full h-full object-cover">
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
