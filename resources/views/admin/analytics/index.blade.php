@extends('layouts.admin')
@section('title', 'التحليلات')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">التحليلات</h1>

{{-- POS Stats --}}
<h2 class="text-white font-bold mb-3">نقطة البيع</h2>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">إجمالي المعاملات</p>
        <p class="text-white text-xl font-bold">{{ $posStats['totalTransactions'] }}</p>
    </div>
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">إجمالي الإيرادات (POS)</p>
        <p class="text-white text-xl font-bold">{{ number_format($posStats['totalRevenue']) }} ج.م</p>
    </div>
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">مبيعات اليوم</p>
        <p class="text-green-400 text-xl font-bold">{{ number_format($posStats['todaySales']) }} ج.م</p>
    </div>
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">معاملات اليوم</p>
        <p class="text-white text-xl font-bold">{{ $posStats['todayTransactions'] }}</p>
    </div>
</div>

{{-- Order Stats --}}
<h2 class="text-white font-bold mb-3">الطلبات الأونلاين</h2>
<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">إجمالي الطلبات</p>
        <p class="text-white text-xl font-bold">{{ $orderStats['totalOrders'] }}</p>
    </div>
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">إجمالي الإيرادات</p>
        <p class="text-white text-xl font-bold">{{ number_format($orderStats['totalRevenue']) }} ج.م</p>
    </div>
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">طلبات معلقة</p>
        <p class="text-yellow-400 text-xl font-bold">{{ $orderStats['pendingOrders'] }}</p>
    </div>
</div>

{{-- Top Products --}}
<h2 class="text-white font-bold mb-3">المنتجات الأكثر مبيعاً</h2>
<div class="bg-brand-dark rounded-xl border border-white/5">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-white/40 text-xs border-b border-white/5">
                <th class="px-4 py-3 text-right">#</th>
                <th class="px-4 py-3 text-right">المنتج</th>
                <th class="px-4 py-3 text-right">المبيعات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $i => $product)
            <tr class="border-b border-white/5">
                <td class="px-4 py-3 text-white/40">{{ $i + 1 }}</td>
                <td class="px-4 py-3 text-white">{{ $product->name_ar ?? $product->name }}</td>
                <td class="px-4 py-3 text-white font-medium">{{ $product->total_sold }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
