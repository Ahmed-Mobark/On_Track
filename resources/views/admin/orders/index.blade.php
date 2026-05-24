@extends('layouts.admin')
@section('title', 'الطلبات')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">الطلبات</h1>

{{-- Filters --}}
<form method="GET" class="flex items-center gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث..."
        class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red w-48">
    <select name="status" onchange="this.form.submit()" class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none">
        <option value="">كل الحالات</option>
        @foreach(['PENDING', 'CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'CANCELLED'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
    </select>
</form>

<div class="bg-brand-dark rounded-xl border border-white/5 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-white/40 text-xs border-b border-white/5">
                <th class="px-4 py-3 text-right">رقم الطلب</th>
                <th class="px-4 py-3 text-right">العميل</th>
                <th class="px-4 py-3 text-right">الإجمالي</th>
                <th class="px-4 py-3 text-right">الدفع</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr class="border-b border-white/5 hover:bg-white/5">
                <td class="px-4 py-3 text-white font-medium">{{ $order->order_number }}</td>
                <td class="px-4 py-3 text-white/70">{{ $order->user->name ?? '-' }}</td>
                <td class="px-4 py-3 text-white">{{ number_format($order->total) }} ج.م</td>
                <td class="px-4 py-3 text-white/60">{{ $order->payment_method }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-1 rounded-full {{ $order->status === 'DELIVERED' ? 'bg-green-500/10 text-green-400' : ($order->status === 'PENDING' ? 'bg-yellow-500/10 text-yellow-400' : 'bg-blue-500/10 text-blue-400') }}">
                        {{ $order->status }}
                    </span>
                </td>
                <td class="px-4 py-3 text-white/40">{{ $order->created_at->format('m/d') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-brand-red text-xs hover:underline">عرض</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
