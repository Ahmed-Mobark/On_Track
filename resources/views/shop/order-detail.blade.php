@extends('layouts.app')
@section('title', 'طلب #' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-white mb-2">طلب #{{ $order->order_number }}</h1>
    <p class="text-white/40 text-sm mb-8">{{ $order->created_at->format('Y/m/d H:i') }}</p>

    <div class="grid md:grid-cols-3 gap-4 mb-8">
        <div class="bg-brand-dark rounded-xl p-4">
            <p class="text-white/40 text-xs mb-1">الحالة</p>
            <p class="text-white font-medium">{{ match($order->status) {
                'PENDING' => 'في انتظار التأكيد',
                'CONFIRMED' => 'تم التأكيد',
                'PROCESSING' => 'قيد التجهيز',
                'SHIPPED' => 'تم الشحن',
                'DELIVERED' => 'تم التوصيل',
                'CANCELLED' => 'ملغي',
                'RETURNED' => 'مرتجع',
                default => $order->status,
            } }}</p>
        </div>
        <div class="bg-brand-dark rounded-xl p-4">
            <p class="text-white/40 text-xs mb-1">حالة الدفع</p>
            <p class="font-medium {{ $order->payment_status === 'PAID' ? 'text-green-400' : 'text-yellow-400' }}">
                {{ match($order->payment_status) {
                    'PENDING' => 'في انتظار التحقق',
                    'PAID' => 'تم التحقق',
                    'FAILED' => 'مرفوض',
                    'REFUNDED' => 'تم الاسترداد',
                    default => $order->payment_status,
                } }}
            </p>
        </div>
        <div class="bg-brand-dark rounded-xl p-4">
            <p class="text-white/40 text-xs mb-1">الإجمالي</p>
            <p class="text-brand-red font-bold text-lg">{{ number_format($order->total) }} ج.م</p>
        </div>
    </div>

    {{-- Payment Breakdown --}}
    @if($order->deposit_amount || $order->wallet_used > 0)
    <div class="bg-brand-dark rounded-xl p-6 mb-6 border border-white/5">
        <h2 class="text-lg font-bold text-white mb-4">تفاصيل الدفع</h2>
        <div class="space-y-3">
            @if($order->wallet_used > 0)
            <div class="flex justify-between items-center py-2">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-400"></div>
                    <span class="text-white/70 text-sm">من رصيد المحفظة</span>
                </div>
                <span class="text-green-400 font-bold text-sm">{{ number_format($order->wallet_used) }} ج.م</span>
            </div>
            @endif
            @if($order->deposit_amount)
            <div class="flex justify-between items-center py-2 {{ $order->wallet_used > 0 ? 'border-t border-white/5' : '' }}">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full {{ $order->payment_status === 'PAID' ? 'bg-green-400' : 'bg-yellow-400' }}"></div>
                    <span class="text-white/70 text-sm">{{ $order->payment_type === 'SHIPPING_ONLY' ? ($order->shipping_cost > 0 ? 'تحويل رسوم الشحن' : 'تحويل عربون التأكيد') : 'تحويل المبلغ (InstaPay)' }}</span>
                </div>
                <span class="text-green-400 font-bold text-sm">{{ number_format($order->deposit_amount) }} ج.م</span>
            </div>
            @endif
            @if($order->payment_type === 'SHIPPING_ONLY')
            @php $totalPaid = ($order->deposit_amount ?? 0) + ($order->wallet_used ?? 0); @endphp
            <div class="flex justify-between items-center py-2 border-t border-white/5">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full {{ $order->status === 'DELIVERED' ? 'bg-green-400' : 'bg-white/30' }}"></div>
                    <span class="text-white/70 text-sm">المتبقي (عند الاستلام)</span>
                </div>
                <span class="text-white font-bold text-sm">{{ number_format(max(0, $order->total - $totalPaid)) }} ج.م</span>
            </div>
            @endif
            <div class="flex justify-between items-center py-2 border-t border-white/10">
                <span class="text-white font-bold text-sm">الإجمالي</span>
                <span class="text-brand-red font-bold">{{ number_format($order->total) }} ج.م</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Items --}}
    <div class="bg-brand-dark rounded-xl p-6 mb-6">
        <h2 class="text-lg font-bold text-white mb-4">المنتجات</h2>
        <div class="space-y-3">
            @foreach($order->items as $item)
                <div class="flex items-center gap-4 py-2 border-b border-white/5 last:border-0">
                    <div class="w-14 h-14 bg-white/5 rounded-lg overflow-hidden">
                        @if($item->product->images->first())
                            <img src="{{ $item->product->images->first()->image_url }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-white text-sm">{{ $item->product->name }}</p>
                        <p class="text-white/40 text-xs">{{ $item->variant->size }} / {{ $item->variant->color }} × {{ $item->quantity }}</p>
                    </div>
                    <span class="text-white font-medium text-sm">{{ number_format($item->price * $item->quantity) }} ج.م</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Tracking --}}
    @if($order->tracking_number)
    <div style="background:rgba(230,57,70,0.05);border:1px solid rgba(230,57,70,0.15);border-radius:16px;padding:20px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <p class="text-white font-bold text-sm">تتبع شحنتك</p>
            <p class="text-white/40 text-xs">{{ $order->shipping_company ?? '' }} - {{ $order->tracking_number }}</p>
            @if($order->shipment_status)
                <p class="text-xs mt-1" style="color:#e63946;">
                    {{ match($order->shipment_status) {
                        'AWAITING_PICKUP' => 'في انتظار الاستلام',
                        'PICKED_UP' => 'تم الاستلام من المخزن',
                        'IN_TRANSIT' => 'في الطريق إليك',
                        'OUT_FOR_DELIVERY' => 'خارج للتوصيل',
                        'DELIVERED' => 'تم التوصيل',
                        'DELIVERY_FAILED' => 'فشل التوصيل',
                        'RETURNED' => 'مرتجع',
                        default => $order->shipment_status,
                    } }}
                </p>
            @endif
        </div>
        <a href="{{ route('order.track', $order) }}" target="_blank"
           style="background:#e63946;color:white;padding:10px 24px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            تتبع الشحنة
        </a>
    </div>
    @endif

    {{-- Address --}}
    @if($order->address)
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-lg font-bold text-white mb-4">عنوان التوصيل</h2>
        <p class="text-white/60 text-sm">{{ $order->address->address }}, {{ $order->address->city }}, {{ $order->address->governorate }}</p>
        <p class="text-white/40 text-xs mt-1">{{ $order->address->phone }}</p>
    </div>
    @endif
</div>
@endsection
