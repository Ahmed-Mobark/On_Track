@extends('layouts.admin')
@section('title', 'تعديل: ' . $product->name)

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">تعديل المنتج</h1>

<form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-6 max-w-3xl">
    @csrf @method('PUT')

    <div class="bg-brand-dark rounded-xl p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">الاسم (إنجليزي)</label>
                <input type="text" name="name" required value="{{ old('name', $product->name) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">الاسم (عربي)</label>
                <input type="text" name="name_ar" value="{{ old('name_ar', $product->name_ar) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">SKU</label>
                <input type="text" name="sku" required value="{{ old('sku', $product->sku) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">الجنس</label>
                <select name="gender" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none">
                    <option value="">الكل</option>
                    <option value="men" {{ $product->gender === 'men' ? 'selected' : '' }}>رجالي</option>
                    <option value="women" {{ $product->gender === 'women' ? 'selected' : '' }}>حريمي</option>
                    <option value="unisex" {{ $product->gender === 'unisex' ? 'selected' : '' }}>يونيسكس</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">السعر</label>
                <input type="number" name="base_price" required step="0.01" value="{{ old('base_price', $product->base_price) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">سعر الخصم</label>
                <input type="number" name="sale_price" step="0.01" value="{{ old('sale_price', $product->sale_price) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
        </div>
        <div>
            <label class="block text-white/70 text-sm mb-1">الوصف</label>
            <textarea name="description" rows="3" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red resize-none">{{ old('description', $product->description) }}</textarea>
        </div>
        <div>
            <label class="block text-white/70 text-sm mb-1">الوصف (عربي)</label>
            <textarea name="description_ar" rows="3" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red resize-none">{{ old('description_ar', $product->description_ar) }}</textarea>
        </div>
    </div>

    {{-- Categories --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-3">التصنيفات</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($categories as $category)
                <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-white/10 cursor-pointer hover:border-brand-red transition-colors text-sm">
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                        {{ $product->categories->contains($category->id) ? 'checked' : '' }}>
                    <span class="text-white/70">{{ $category->name_ar ?? $category->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Options --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <div class="flex flex-wrap gap-4">
            <label class="flex items-center gap-2 text-white/70 text-sm">
                <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}> نشط
            </label>
            <label class="flex items-center gap-2 text-white/70 text-sm">
                <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }}> مميز
            </label>
            <label class="flex items-center gap-2 text-white/70 text-sm">
                <input type="checkbox" name="is_best_seller" value="1" {{ $product->is_best_seller ? 'checked' : '' }}> الأكثر مبيعاً
            </label>
        </div>
    </div>

    <button type="submit" class="bg-brand-red hover:bg-brand-red-dark text-white px-8 py-3 rounded-xl font-semibold transition-colors">
        تحديث المنتج
    </button>
</form>
@endsection
