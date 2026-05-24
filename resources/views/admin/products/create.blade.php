@extends('layouts.admin')
@section('title', 'إضافة منتج')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">إضافة منتج جديد</h1>

<form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6 max-w-3xl">
    @csrf

    <div class="bg-brand-dark rounded-xl p-6 space-y-4">
        <h2 class="text-white font-bold mb-2">البيانات الأساسية</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">الاسم (إنجليزي)</label>
                <input type="text" name="name" required value="{{ old('name') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">الاسم (عربي)</label>
                <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">SKU</label>
                <input type="text" name="sku" required value="{{ old('sku') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">الجنس</label>
                <select name="gender" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none">
                    <option value="">الكل</option>
                    <option value="men">رجالي</option>
                    <option value="women">حريمي</option>
                    <option value="unisex">يونيسكس</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">السعر</label>
                <input type="number" name="base_price" required step="0.01" value="{{ old('base_price') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">سعر الخصم</label>
                <input type="number" name="sale_price" step="0.01" value="{{ old('sale_price') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
        </div>
        <div>
            <label class="block text-white/70 text-sm mb-1">الوصف</label>
            <textarea name="description" rows="3" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red resize-none">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-white/70 text-sm mb-1">الوصف (عربي)</label>
            <textarea name="description_ar" rows="3" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red resize-none">{{ old('description_ar') }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">الخامات</label>
                <input type="text" name="materials" value="{{ old('materials') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">تعليمات العناية</label>
                <input type="text" name="care_instructions" value="{{ old('care_instructions') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
        </div>
    </div>

    {{-- Categories --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-3">التصنيفات</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($categories as $category)
                <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-white/10 cursor-pointer hover:border-brand-red transition-colors text-sm">
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                    <span class="text-white/70">{{ $category->name_ar ?? $category->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Options --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-3">خيارات</h2>
        <div class="flex flex-wrap gap-4">
            <label class="flex items-center gap-2 text-white/70 text-sm">
                <input type="checkbox" name="is_active" value="1" checked> نشط
            </label>
            <label class="flex items-center gap-2 text-white/70 text-sm">
                <input type="checkbox" name="is_featured" value="1"> مميز
            </label>
            <label class="flex items-center gap-2 text-white/70 text-sm">
                <input type="checkbox" name="is_best_seller" value="1"> الأكثر مبيعاً
            </label>
        </div>
    </div>

    {{-- Tags --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-3">التاجات</h2>
        <input type="text" name="tags" placeholder="مفصولة بفاصلة: رياضي, قطن, صيفي"
            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red">
    </div>

    <button type="submit" class="bg-brand-red hover:bg-brand-red-dark text-white px-8 py-3 rounded-xl font-semibold transition-colors">
        حفظ المنتج
    </button>
</form>
@endsection
