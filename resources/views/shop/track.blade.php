@extends('layouts.app')
@section('title', 'تتبع الطلب #' . $order->order_number)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-white mb-2">تتبع الطلب</h1>
    <p class="text-white/40 text-sm mb-8">#{{ $order->order_number }}</p>

    {{-- Status Timeline --}}
    @php
        $statuses = [
            'PENDING' => ['label' => 'تم استلام الطلب', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            'CONFIRMED' => ['label' => 'تم تأكيد الطلب', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            'PROCESSING' => ['label' => 'جاري التجهيز', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
            'SHIPPED' => ['label' => 'تم الشحن', 'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
            'DELIVERED' => ['label' => 'تم التوصيل', 'icon' => 'M5 13l4 4L19 7'],
        ];
        $orderSteps = array_keys($statuses);
        $currentIdx = array_search($order->status, $orderSteps);
        if ($currentIdx === false) $currentIdx = -1;
        $isCancelled = in_array($order->status, ['CANCELLED', 'RETURNED']);
    @endphp

    @if($isCancelled)
        <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-6 text-center mb-8">
            <p class="text-red-400 font-bold text-lg">{{ $order->status === 'CANCELLED' ? 'تم إلغاء الطلب' : 'تم إرجاع الطلب' }}</p>
        </div>
    @else
        <div class="bg-brand-dark rounded-xl p-6 mb-8">
            <div class="space-y-0">
                @foreach($statuses as $key => $step)
                    @php
                        $stepIdx = array_search($key, $orderSteps);
                        $isDone = $stepIdx <= $currentIdx;
                        $isCurrent = $stepIdx === $currentIdx;
                        $isLast = $loop->last;
                    @endphp
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $isDone ? 'bg-brand-red' : 'bg-white/10' }} transition-colors">
                                <svg class="w-5 h-5 {{ $isDone ? 'text-white' : 'text-white/30' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                                </svg>
                            </div>
                            @if(!$isLast)
                                <div class="w-0.5 h-12 {{ $isDone && !$isCurrent ? 'bg-brand-red' : 'bg-white/10' }}"></div>
                            @endif
                        </div>
                        <div class="pt-2 pb-4">
                            <p class="text-sm font-medium {{ $isDone ? 'text-white' : 'text-white/30' }}">{{ $step['label'] }}</p>
                            @if($isCurrent)
                                <p class="text-brand-red text-xs mt-0.5">الحالة الحالية</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Shipping info --}}
    @if($order->tracking_number)
    <div class="bg-brand-dark rounded-xl p-6 mb-6">
        <h2 class="text-white font-bold mb-3">بيانات الشحن</h2>
        <div class="space-y-2 text-sm">
            <p class="text-white/60">شركة الشحن: <span class="text-white">{{ $order->shipping_company }}</span></p>
            <p class="text-white/60">رقم التتبع: <span class="text-white font-mono" dir="ltr">{{ $order->tracking_number }}</span></p>
            @if($order->shipment_status)
                @php
                    $shipLabels = [
                        'AWAITING_PICKUP' => 'في انتظار الاستلام',
                        'PICKED_UP' => 'تم الاستلام من المخزن',
                        'IN_TRANSIT' => 'في الطريق',
                        'OUT_FOR_DELIVERY' => 'خارج للتوصيل',
                        'DELIVERED' => 'تم التوصيل',
                        'DELIVERY_FAILED' => 'فشل التوصيل',
                        'RETURNED' => 'مرتجع',
                    ];
                @endphp
                <p class="text-white/60">حالة الشحنة: <span class="text-brand-red font-medium">{{ $shipLabels[$order->shipment_status] ?? $order->shipment_status }}</span></p>
            @endif
        </div>
    </div>
    @endif

    {{-- Order items --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-3">تفاصيل الطلب</h2>
        @foreach($order->items as $item)
            <div class="flex items-center gap-3 py-3 border-b border-white/5 last:border-0">
                <div class="flex-1">
                    <p class="text-white text-sm">{{ $item->product->name_ar ?? $item->product->name }}</p>
                    <p class="text-white/40 text-xs">{{ $item->variant->size ?? '' }}/{{ $item->variant->color ?? '' }} x {{ $item->quantity }}</p>
                </div>
                <span class="text-white text-sm font-medium">{{ number_format($item->price * $item->quantity) }} ج.م</span>
            </div>
        @endforeach
        <div class="mt-3 pt-3 border-t border-white/10 flex justify-between text-white font-bold">
            <span>الإجمالي</span>
            <span class="text-brand-red">{{ number_format($order->total) }} ج.م</span>
        </div>
    </div>
</div>
@endsection
