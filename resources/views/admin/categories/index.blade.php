@extends('layouts.admin')
@section('title', 'التصنيفات')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">التصنيفات</h1>
</div>

<div class="grid md:grid-cols-2 gap-6">
    {{-- Add Form --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-4">إضافة تصنيف</h2>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-3">
            @csrf
            <input type="text" name="name" placeholder="الاسم (إنجليزي)" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red">
            <input type="text" name="name_ar" placeholder="الاسم (عربي)" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red">
            <textarea name="description" placeholder="الوصف" rows="2" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red resize-none"></textarea>
            <select name="parent_id" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none">
                <option value="">بدون أب (تصنيف رئيسي)</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name_ar ?? $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-brand-red hover:bg-brand-red-dark text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">إضافة</button>
        </form>
    </div>

    {{-- List --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-4">التصنيفات الحالية</h2>
        <div class="space-y-2">
            @foreach($categories as $category)
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <div>
                        <span class="text-white text-sm font-medium">{{ $category->name_ar ?? $category->name }}</span>
                        <span class="text-white/30 text-xs mr-2">({{ $category->products_count }} منتج)</span>
                    </div>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 text-xs hover:underline">حذف</button>
                    </form>
                </div>
                @foreach($category->children as $child)
                    <div class="flex items-center justify-between py-2 border-b border-white/5 pr-6">
                        <span class="text-white/60 text-sm">↳ {{ $child->name_ar ?? $child->name }}</span>
                        <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 text-xs hover:underline">حذف</button>
                        </form>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
@endsection
