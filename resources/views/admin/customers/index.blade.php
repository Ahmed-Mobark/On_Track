@extends('layouts.admin')
@section('title', 'العملاء')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">العملاء</h1>

<form method="GET" class="mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو الإيميل..."
        class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red w-64">
</form>

<div class="bg-brand-dark rounded-xl border border-white/5 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-white/40 text-xs border-b border-white/5">
                <th class="px-4 py-3 text-right">الاسم</th>
                <th class="px-4 py-3 text-right">الإيميل</th>
                <th class="px-4 py-3 text-right">الموبايل</th>
                <th class="px-4 py-3 text-right">الطلبات</th>
                <th class="px-4 py-3 text-right">تاريخ التسجيل</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr class="border-b border-white/5 hover:bg-white/5 cursor-pointer" onclick="window.location='{{ route('admin.customers.show', $customer) }}'">
                <td class="px-4 py-3 text-white font-medium">{{ $customer->name }}</td>
                <td class="px-4 py-3 text-white/60">{{ $customer->email }}</td>
                <td class="px-4 py-3 text-white/60">{{ $customer->phone ?? '-' }}</td>
                <td class="px-4 py-3 text-white">{{ $customer->orders_count }}</td>
                <td class="px-4 py-3 text-white/40">{{ $customer->created_at->format('Y/m/d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $customers->links() }}</div>
@endsection
