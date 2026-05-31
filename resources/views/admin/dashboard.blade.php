@extends('layouts.admin')
@section('title', 'لوحة التحكم')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">لوحة التحكم</h1>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'إجمالي الطلبات', 'value' => $stats['totalOrders'], 'url' => route('admin.orders.index')],
        ['label' => 'إجمالي الإيرادات', 'value' => number_format($stats['totalRevenue']) . ' ج.م', 'url' => route('admin.analytics.index')],
        ['label' => 'طلبات معلقة', 'value' => $stats['pendingOrders'], 'url' => route('admin.orders.index') . '?status=PENDING'],
        ['label' => 'مبيعات POS اليوم', 'value' => number_format($stats['todayPOSSales']) . ' ج.م', 'url' => route('admin.pos.index')],
        ['label' => 'عدد العملاء', 'value' => $stats['totalCustomers'], 'url' => route('admin.customers.index')],
        ['label' => 'عدد المنتجات', 'value' => $stats['totalProducts'], 'url' => route('admin.products.index')],
        ['label' => 'طلبات اليوم', 'value' => $stats['todayOrders'], 'url' => route('admin.orders.index')],
        ['label' => 'في المفضلة', 'value' => $stats['totalWishlistItems'], 'url' => route('admin.products.index')],
    ] as $stat)
        <a href="{{ $stat['url'] }}" class="bg-brand-dark rounded-xl p-4 border border-white/5 hover:border-white/15 hover:bg-white/[0.02] transition-all">
            <p class="text-white/40 text-xs mb-1">{{ $stat['label'] }}</p>
            <p class="text-white text-xl font-bold">{{ $stat['value'] }}</p>
        </a>
    @endforeach
</div>

{{-- Best Selling & Most Wishlisted --}}
<div class="grid md:grid-cols-2 gap-4 mb-8">
    {{-- Best Selling --}}
    <div class="bg-brand-dark rounded-xl border border-white/5">
        <div class="p-4 border-b border-white/5">
            <h2 class="text-white font-bold">الأكثر مبيعاً</h2>
        </div>
        <div class="p-4 space-y-3">
            @forelse($bestSelling as $i => $item)
                <a href="{{ route('admin.products.edit', $item->product) }}" class="flex items-center gap-3 hover:bg-white/5 rounded-lg px-2 py-1 -mx-2 transition-colors">
                    <span class="text-white/20 font-bold text-lg w-6 text-center">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium truncate">{{ $item->product->name_ar ?? $item->product->name }}</p>
                        <p class="text-white/40 text-xs">{{ $item->total_sold }} قطعة</p>
                    </div>
                    <span class="text-green-400 text-sm font-bold">{{ number_format($item->total_revenue) }} ج.م</span>
                </a>
            @empty
                <p class="text-white/30 text-sm text-center py-4">لا توجد مبيعات بعد</p>
            @endforelse
        </div>
    </div>

    {{-- Most Wishlisted --}}
    <div class="bg-brand-dark rounded-xl border border-white/5">
        <div class="p-4 border-b border-white/5">
            <h2 class="text-white font-bold">الأكثر في المفضلة</h2>
        </div>
        <div class="p-4 space-y-3">
            @forelse($mostWishlisted as $i => $item)
                <a href="{{ route('admin.products.edit', $item->product) }}" class="flex items-center gap-3 hover:bg-white/5 rounded-lg px-2 py-1 -mx-2 transition-colors">
                    <span class="text-white/20 font-bold text-lg w-6 text-center">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium truncate">{{ $item->product->name_ar ?? $item->product->name }}</p>
                    </div>
                    <div class="flex items-center gap-1 text-red-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span class="text-sm font-bold">{{ $item->wishlist_count }}</span>
                    </div>
                </a>
            @empty
                <p class="text-white/30 text-sm text-center py-4">لا توجد عناصر في المفضلة بعد</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Recent Orders --}}
<div class="bg-brand-dark rounded-xl border border-white/5">
    <div class="p-4 border-b border-white/5 flex items-center justify-between">
        <h2 class="text-white font-bold">أحدث الطلبات</h2>
        <a href="{{ route('admin.orders.index') }}" class="text-brand-red text-sm hover:underline">عرض الكل</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-white/40 text-xs border-b border-white/5">
                    <th class="px-4 py-3 text-right">رقم الطلب</th>
                    <th class="px-4 py-3 text-right">العميل</th>
                    <th class="px-4 py-3 text-right">الإجمالي</th>
                    <th class="px-4 py-3 text-right">الحالة</th>
                    <th class="px-4 py-3 text-right">التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr class="border-b border-white/5 hover:bg-white/5 cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                    <td class="px-4 py-3"><span class="text-white font-medium">{{ $order->order_number }}</span></td>
                    <td class="px-4 py-3">
                        @if($order->user)
                        <a href="{{ route('admin.customers.show', $order->user) }}" class="text-white/70 hover:text-brand-red" onclick="event.stopPropagation()">{{ $order->user->name }}</a>
                        @else <span class="text-white/40">-</span> @endif
                    </td>
                    <td class="px-4 py-3 text-white font-medium">{{ number_format($order->total) }} ج.م</td>
                    <td class="px-4 py-3">
                        @php
                            $sc = ['PENDING'=>'bg-yellow-500/10 text-yellow-400','CONFIRMED'=>'bg-blue-500/10 text-blue-400','PROCESSING'=>'bg-purple-500/10 text-purple-400','SHIPPED'=>'bg-indigo-500/10 text-indigo-400','DELIVERED'=>'bg-green-500/10 text-green-400','CANCELLED'=>'bg-red-500/10 text-red-400','RETURNED'=>'bg-red-500/10 text-red-400'];
                            $sl = ['PENDING'=>'معلق','CONFIRMED'=>'مؤكد','PROCESSING'=>'تجهيز','SHIPPED'=>'شحن','DELIVERED'=>'توصيل','CANCELLED'=>'ملغي','RETURNED'=>'مرتجع'];
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full {{ $sc[$order->status] ?? 'bg-white/10 text-white/60' }}">
                            {{ $sl[$order->status] ?? $order->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-white/40">{{ $order->created_at->format('m/d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
