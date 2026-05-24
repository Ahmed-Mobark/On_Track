@extends('layouts.app')
@section('title', 'طلباتي')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-white mb-8">طلباتي</h1>

    @if($orders->count())
        <div class="space-y-4">
            @foreach($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="block bg-brand-dark rounded-xl p-4 hover:bg-white/5 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white font-medium text-sm">{{ $order->order_number }}</span>
                        <span class="text-xs px-2 py-1 rounded-full {{ $order->status === 'DELIVERED' ? 'bg-green-500/10 text-green-400' : 'bg-yellow-500/10 text-yellow-400' }}">
                            {{ $order->status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-white/40 text-xs">
                        <span>{{ $order->created_at->format('Y/m/d') }}</span>
                        <span class="text-white font-bold">{{ number_format($order->total) }} ج.م</span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @else
        <div class="text-center py-16">
            <p class="text-white/40 text-lg mb-4">لا توجد طلبات</p>
            <a href="{{ route('shop') }}" class="text-brand-red hover:underline">تصفح المتجر</a>
        </div>
    @endif
</div>
@endsection
