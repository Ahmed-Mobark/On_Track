@extends('layouts.admin')
@section('title', 'المنتجات')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">المنتجات</h1>
    <a href="{{ route('admin.products.create') }}" class="bg-brand-red hover:bg-brand-red-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
        + إضافة منتج
    </a>
</div>

{{-- Search --}}
<form method="GET" class="mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو SKU..."
        class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red w-64">
</form>

{{-- Table --}}
<div class="bg-brand-dark rounded-xl border border-white/5 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-white/40 text-xs border-b border-white/5">
                <th class="px-4 py-3 text-right">المنتج</th>
                <th class="px-4 py-3 text-right">SKU</th>
                <th class="px-4 py-3 text-right">السعر</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr class="border-b border-white/5 hover:bg-white/5 cursor-pointer" onclick="if(!event.target.closest('form,a,button'))window.location='{{ route('admin.products.edit', $product) }}'">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/5 rounded-lg overflow-hidden">
                            @if($product->images->first())
                                <img src="{{ $product->images->first()->image_url }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <span class="text-white">{{ $product->name_ar ?? $product->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-white/60">{{ $product->sku }}</td>
                <td class="px-4 py-3 text-white">{{ number_format($product->base_price) }} ج.م</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-1 rounded-full {{ $product->is_active ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                        {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-400 hover:underline text-xs">تعديل</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:underline text-xs">حذف</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
