@extends('layouts.app')
@section('title', 'تم الطلب بنجاح')

@push('styles')
<style>
    @keyframes checkDraw { from { stroke-dashoffset: 24; } to { stroke-dashoffset: 0; } }
    @keyframes ringPulse {
        0% { transform: scale(0.8); opacity: 0; }
        50% { transform: scale(1); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes confettiIn { from { opacity: 0; transform: translateY(20px) scale(0.9); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .success-ring { animation: ringPulse 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.1s both; }
    .success-check svg path { stroke-dasharray: 24; animation: checkDraw 0.4s ease-out 0.4s both; }
    .success-title { animation: confettiIn 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.5s both; }
    .success-sub { animation: confettiIn 0.4s ease-out 0.6s both; }
    .success-card { animation: confettiIn 0.4s ease-out 0.7s both; }
    .success-actions { animation: confettiIn 0.4s ease-out 0.8s both; }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-16 sm:py-20 text-center">
    <div class="success-ring bg-green-500/10 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8 ring-4 ring-green-500/5">
        <div class="success-check bg-green-500/20 w-16 h-16 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
    </div>

    <h1 class="success-title text-3xl font-black text-white mb-3">تم تقديم طلبك بنجاح!</h1>
    <p class="success-sub text-white/50 text-lg mb-2">رقم الطلب: <span class="text-white font-bold bg-white/5 px-3 py-1 rounded-lg">{{ $order->order_number }}</span></p>
    <p class="success-sub text-white/35 text-sm mb-10">هتوصلك رسالة تأكيد. شكراً لثقتك في On Track</p>

    <div class="bg-brand-dark rounded-xl p-6 text-right mb-8">
        <h2 class="text-white font-bold mb-3">تفاصيل الطلب</h2>
        @foreach($order->items as $item)
            <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                <div>
                    <span class="text-white text-sm">{{ $item->product->name ?? '' }}</span>
                    <span class="text-white/40 text-xs mr-2">{{ $item->variant->size ?? '' }}/{{ $item->variant->color ?? '' }} × {{ $item->quantity }}</span>
                </div>
                <span class="text-white text-sm font-medium">{{ number_format($item->price * $item->quantity) }} ج.م</span>
            </div>
        @endforeach
        <div class="flex justify-between text-white font-bold text-lg mt-3 pt-3 border-t border-white/10">
            <span>الإجمالي</span>
            <span class="text-brand-red">{{ number_format($order->total) }} ج.م</span>
        </div>
    </div>

    {{-- Payment Breakdown --}}
    @if($order->deposit_amount)
    <div class="bg-brand-dark rounded-xl p-6 text-right mb-8">
        <h2 class="text-white font-bold mb-3">تفاصيل الدفع</h2>
        <div class="space-y-2">
            <div class="flex justify-between items-center py-2">
                <span class="text-white/60 text-sm">{{ $order->payment_type === 'SHIPPING_ONLY' ? ($order->shipping_cost > 0 ? 'رسوم الشحن (مدفوع)' : 'عربون التأكيد (مدفوع)') : 'المبلغ الكامل (مدفوع)' }}</span>
                <span class="text-green-400 font-bold">{{ number_format($order->deposit_amount) }} ج.م</span>
            </div>
            @if($order->payment_type === 'SHIPPING_ONLY')
            <div class="flex justify-between items-center py-2 border-t border-white/5">
                <span class="text-white/60 text-sm">المتبقي عند الاستلام</span>
                <span class="text-white font-bold">{{ number_format($order->total - $order->deposit_amount) }} ج.م</span>
            </div>
            @endif
        </div>
        <div class="mt-3 pt-3 border-t border-white/10">
            <p class="text-white/40 text-xs flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                جاري التحقق من إثبات الدفع. هيتم تأكيد طلبك في أقرب وقت.
            </p>
        </div>
    </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('shop') }}" class="bg-brand-red hover:bg-brand-red-dark text-white px-8 py-3 rounded-xl font-semibold transition-colors">
            تابع التسوق
        </a>
        <a href="https://wa.me/201010300353?text={{ urlencode('مرحباً، أريد متابعة طلبي رقم ' . $order->order_number) }}" target="_blank"
           class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            تواصل معنا
        </a>
    </div>
</div>
@endsection
