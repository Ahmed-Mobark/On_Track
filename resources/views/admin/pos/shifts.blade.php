@extends('layouts.admin')
@section('title', 'الورديات')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">الورديات</h1>

<div class="bg-brand-dark rounded-xl border border-white/5 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-white/40 text-xs border-b border-white/5">
                <th class="px-4 py-3 text-right">الكاشير</th>
                <th class="px-4 py-3 text-right">المبلغ الافتتاحي</th>
                <th class="px-4 py-3 text-right">المبيعات</th>
                <th class="px-4 py-3 text-right">المعاملات</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">الفتح</th>
                <th class="px-4 py-3 text-right">الإغلاق</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
            <tr class="border-b border-white/5 hover:bg-white/5">
                <td class="px-4 py-3 text-white">{{ $session->cashier->name ?? '-' }}</td>
                <td class="px-4 py-3 text-white/60">{{ number_format($session->opening_cash) }}</td>
                <td class="px-4 py-3 text-white font-medium">{{ number_format($session->total_sales) }} ج.م</td>
                <td class="px-4 py-3 text-white/60">{{ $session->transactions_count }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-1 rounded-full {{ $session->is_open ? 'bg-green-500/10 text-green-400' : 'bg-white/10 text-white/40' }}">
                        {{ $session->is_open ? 'مفتوحة' : 'مغلقة' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-white/40 text-xs">{{ $session->opened_at?->format('m/d H:i') }}</td>
                <td class="px-4 py-3 text-white/40 text-xs">{{ $session->closed_at?->format('m/d H:i') ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $sessions->links() }}</div>
@endsection
