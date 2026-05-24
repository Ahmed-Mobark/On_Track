@extends('layouts.admin')
@section('title', 'المخزون')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">إدارة المخزون</h1>

{{-- Low Stock Alert --}}
@if($lowStock->count())
<div class="bg-red-500/5 border border-red-500/20 rounded-xl p-4 mb-6">
    <h2 class="text-red-400 font-bold text-sm mb-3">تنبيه: مخزون منخفض (��� 5)</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        @foreach($lowStock as $variant)
            <div class="flex items-center justify-between py-2 px-3 bg-red-500/5 rounded-lg">
                <div>
                    <span class="text-white text-xs font-medium">{{ $variant->product->name ?? '' }}</span>
                    <span class="text-white/40 text-xs mr-2">{{ $variant->size }}/{{ $variant->color }}</span>
                </div>
                <span class="text-red-400 font-bold text-sm">{{ $variant->quantity }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Adjust Stock --}}
<div class="bg-brand-dark rounded-xl p-6 mb-6 border border-white/5">
    <h2 class="text-white font-bold mb-4">تعديل مخزون</h2>
    <form action="{{ route('admin.inventory.adjust') }}" method="POST" class="flex flex-wrap items-end gap-3">
        @csrf
        <div>
            <label class="block text-white/70 text-xs mb-1">Variant ID</label>
            <input type="text" name="variant_id" required placeholder="UUID" class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm w-64 focus:outline-none focus:border-brand-red" dir="ltr">
        </div>
        <div>
            <label class="block text-white/70 text-xs mb-1">الكمية</label>
            <input type="number" name="quantity" required min="1" class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm w-24 focus:outline-none" dir="ltr">
        </div>
        <div>
            <label class="block text-white/70 text-xs mb-1">الإجراء</label>
            <select name="action" class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none">
                <option value="NEW_STOCK">مخزون جديد</option>
                <option value="MANUAL_ADJUSTMENT">تعديل يدوي</option>
                <option value="DAMAGED">تالف</option>
            </select>
        </div>
        <div>
            <label class="block text-white/70 text-xs mb-1">ملاحظات</label>
            <input type="text" name="notes" class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm w-40 focus:outline-none">
        </div>
        <button type="submit" class="bg-brand-red hover:bg-brand-red-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">تنفيذ</button>
    </form>
</div>

{{-- Inventory Logs --}}
<div class="bg-brand-dark rounded-xl border border-white/5 overflow-x-auto">
    <div class="p-4 border-b border-white/5">
        <h2 class="text-white font-bold">سجل الحركات</h2>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-white/40 text-xs border-b border-white/5">
                <th class="px-4 py-3 text-right">المنتج</th>
                <th class="px-4 py-3 text-right">الإجراء</th>
                <th class="px-4 py-3 text-right">الكمية</th>
                <th class="px-4 py-3 text-right">قبل</th>
                <th class="px-4 py-3 text-right">بعد</th>
                <th class="px-4 py-3 text-right">التاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr class="border-b border-white/5">
                <td class="px-4 py-3 text-white text-xs">{{ $log->variant->product->name ?? '' }} ({{ $log->variant->size ?? '' }}/{{ $log->variant->color ?? '' }})</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-0.5 rounded {{ $log->action === 'SALE' ? 'bg-blue-500/10 text-blue-400' : ($log->action === 'RETURN' ? 'bg-green-500/10 text-green-400' : 'bg-yellow-500/10 text-yellow-400') }}">
                        {{ $log->action }}
                    </span>
                </td>
                <td class="px-4 py-3 text-white">{{ $log->quantity }}</td>
                <td class="px-4 py-3 text-white/40">{{ $log->previous_qty }}</td>
                <td class="px-4 py-3 text-white/70">{{ $log->new_qty }}</td>
                <td class="px-4 py-3 text-white/40 text-xs">{{ $log->created_at?->format('m/d H:i') ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
