@extends('layouts.admin')
@section('title', $user->name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.customers.index') }}" class="text-white/40 hover:text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
    <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">الطلبات</p>
        <p class="text-white text-2xl font-bold">{{ $user->orders_count }}</p>
    </div>
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">إجمالي المشتريات</p>
        <p class="text-brand-red text-2xl font-bold">{{ number_format($totalSpent) }} <span class="text-sm text-white/40">ج.م</span></p>
    </div>
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">النقاط</p>
        <p class="text-yellow-400 text-2xl font-bold">{{ number_format($wallet->points) }}</p>
    </div>
    <div class="bg-brand-dark rounded-xl p-4 border border-white/5">
        <p class="text-white/40 text-xs">رصيد المحفظة</p>
        <p class="text-green-400 text-2xl font-bold">{{ number_format($wallet->balance) }} <span class="text-sm text-green-400/60">ج.م</span></p>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-6 mb-6">
    {{-- Customer Info --}}
    <div class="bg-brand-dark rounded-xl p-6 border border-white/5">
        <h2 class="text-white font-bold mb-4">بيانات العميل</h2>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-white/40">الاسم</span><span class="text-white">{{ $user->first_name }} {{ $user->last_name }}</span></div>
            <div class="flex justify-between"><span class="text-white/40">الإيميل</span><span class="text-white" dir="ltr">{{ $user->email }}</span></div>
            <div class="flex justify-between"><span class="text-white/40">الموبايل</span><span class="text-white" dir="ltr">{{ $user->phone ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-white/40">تاريخ التسجيل</span><span class="text-white">{{ $user->created_at->format('Y/m/d') }}</span></div>
        </div>

        @if($user->phone)
        @php
            $phone = preg_replace('/[^0-9]/', '', $user->phone);
            if (str_starts_with($phone, '0')) $phone = '2' . $phone;
            if (!str_starts_with($phone, '20')) $phone = '20' . $phone;
        @endphp
        <div class="mt-4 flex gap-2">
            <a href="https://wa.me/{{ $phone }}" target="_blank"
                style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;background:#22c55e;color:white;font-weight:600;padding:10px;border-radius:10px;font-size:13px;text-decoration:none;">
                <svg style="width:16px;height:16px;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                واتساب
            </a>
            <a href="tel:+{{ $phone }}"
                style="display:flex;align-items:center;justify-content:center;gap:6px;background:rgba(255,255,255,0.05);color:white;padding:10px 20px;border-radius:10px;font-size:13px;text-decoration:none;border:1px solid rgba(255,255,255,0.1);">
                اتصل
            </a>
        </div>
        @endif
    </div>

    {{-- Wallet Management --}}
    <div class="bg-brand-dark rounded-xl p-6 border border-white/5">
        <h2 class="text-white font-bold mb-4">إدارة المحفظة والنقاط</h2>

        <form action="{{ route('admin.customers.wallet', $user) }}" method="POST" class="space-y-3" id="wallet-form">
            @csrf
            <div>
                <label class="block text-white/70 text-sm mb-1">العملية</label>
                <select name="action" id="wallet-action" onchange="updateWalletLabel()"
                    style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px;color:white;font-size:13px;">
                    <option value="add_balance" style="background:#141414;">إضافة رصيد (ج.م)</option>
                    <option value="deduct_balance" style="background:#141414;">خصم رصيد (ج.م)</option>
                    <option value="add_points" style="background:#141414;">إضافة نقاط</option>
                    <option value="deduct_points" style="background:#141414;">خصم نقاط</option>
                </select>
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1" id="amount-label">المبلغ (ج.م)</label>
                <input type="number" name="amount" required min="0.01" step="0.01" placeholder="0"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">السبب (اختياري)</label>
                <input type="text" name="reason" placeholder="مثال: استرجاع منتج / هدية / تعويض"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
            <button type="submit" style="width:100%;background:#e63946;color:white;padding:10px;border-radius:10px;border:none;cursor:pointer;font-size:14px;font-weight:700;">
                تنفيذ
            </button>
        </form>

        <script>
        function updateWalletLabel() {
            var action = document.getElementById('wallet-action').value;
            var label = document.getElementById('amount-label');
            if (action.includes('points')) {
                label.textContent = 'عدد النقاط';
            } else {
                label.textContent = 'المبلغ (ج.م)';
            }
        }
        </script>
    </div>
</div>

{{-- Wallet Transactions --}}
@if($transactions->count())
<div class="bg-brand-dark rounded-xl p-6 mb-6 border border-white/5">
    <h2 class="text-white font-bold mb-4">سجل المعاملات</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-white/40 text-xs border-b border-white/5">
                    <th class="px-3 py-2 text-right">التاريخ</th>
                    <th class="px-3 py-2 text-right">النوع</th>
                    <th class="px-3 py-2 text-right">الوصف</th>
                    <th class="px-3 py-2 text-right">المبلغ</th>
                    <th class="px-3 py-2 text-right">النقاط</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                <tr class="border-b border-white/5">
                    <td class="px-3 py-2 text-white/40 text-xs">{{ $tx->created_at->format('m/d H:i') }}</td>
                    <td class="px-3 py-2">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $tx->type === 'CREDIT' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                            {{ $tx->type === 'CREDIT' ? 'إضافة' : 'خصم' }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-white/70 text-xs">{{ $tx->description }}</td>
                    <td class="px-3 py-2 font-medium {{ $tx->type === 'CREDIT' ? 'text-green-400' : 'text-red-400' }}">
                        @if($tx->amount > 0){{ $tx->type === 'CREDIT' ? '+' : '-' }}{{ number_format($tx->amount) }} ج.م @else - @endif
                    </td>
                    <td class="px-3 py-2 font-medium {{ $tx->type === 'CREDIT' ? 'text-green-400' : 'text-red-400' }}">
                        @if($tx->points > 0){{ $tx->type === 'CREDIT' ? '+' : '-' }}{{ number_format($tx->points) }} @else - @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Recent Orders --}}
@if($orders->count())
<div class="bg-brand-dark rounded-xl p-6 border border-white/5">
    <h2 class="text-white font-bold mb-4">آخر الطلبات</h2>
    <div class="space-y-2">
        @foreach($orders as $order)
        <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-white/5 transition-colors">
            <div>
                <span class="text-white text-sm font-medium">{{ $order->order_number }}</span>
                <span class="text-white/40 text-xs mr-2">{{ $order->created_at->format('m/d') }}</span>
                @php
                    $statusColors = ['PENDING' => 'text-yellow-400', 'CONFIRMED' => 'text-blue-400', 'SHIPPED' => 'text-purple-400', 'DELIVERED' => 'text-green-400', 'CANCELLED' => 'text-red-400', 'RETURNED' => 'text-red-400'];
                    $statusLabels = ['PENDING' => 'معلق', 'CONFIRMED' => 'مؤكد', 'PROCESSING' => 'تجهيز', 'SHIPPED' => 'شحن', 'DELIVERED' => 'توصيل', 'CANCELLED' => 'ملغي', 'RETURNED' => 'مرتجع'];
                @endphp
                <span class="text-xs {{ $statusColors[$order->status] ?? 'text-white/40' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
            </div>
            <span class="text-white font-bold text-sm">{{ number_format($order->total) }} ج.م</span>
        </a>
        @endforeach
    </div>
</div>
@endif
@endsection
