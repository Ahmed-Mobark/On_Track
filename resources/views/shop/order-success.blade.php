@extends('layouts.app')
@section('title', 'تم الطلب بنجاح')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16 text-center">
    <div class="bg-green-500/10 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-3xl font-bold text-white mb-3">تم تقديم طلبك بنجاح!</h1>
    <p class="text-white/50 text-lg mb-2">رقم الطلب: <span class="text-white font-bold">{{ $order->order_number }}</span></p>
    <p class="text-white/40 text-sm mb-8">هتوصلك رسالة تأكيد. شكراً لثقتك في On Track</p>

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
