@extends('layouts.admin')
@section('title', 'المرتجعات')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">المرتجعات</h1>

<div class="bg-brand-dark rounded-xl border border-white/5 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-white/40 text-xs border-b border-white/5">
                <th class="px-4 py-3 text-right">رقم المعاملة</th>
                <th class="px-4 py-3 text-right">الكاشير</th>
                <th class="px-4 py-3 text-right">المبلغ</th>
                <th class="px-4 py-3 text-right">السبب</th>
                <th class="px-4 py-3 text-right">التاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($returns as $return)
            <tr class="border-b border-white/5 hover:bg-white/5">
                <td class="px-4 py-3 text-white font-mono text-xs">{{ $return->transaction_number }}</td>
                <td class="px-4 py-3 text-white/70">{{ $return->cashier->name ?? '-' }}</td>
                <td class="px-4 py-3 text-red-400 font-medium">{{ number_format($return->total) }} ج.م</td>
                <td class="px-4 py-3 text-white/60">{{ $return->return_reason }}</td>
                <td class="px-4 py-3 text-white/40 text-xs">{{ $return->created_at->format('m/d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $returns->links() }}</div>
@endsection
