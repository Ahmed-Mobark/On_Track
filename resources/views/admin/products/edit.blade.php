@extends('layouts.admin')
@section('title', 'تعديل: ' . $product->name)

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">تعديل المنتج</h1>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-3xl">
    @csrf @method('PUT')

    {{-- Basic Info --}}
    <div class="bg-brand-dark rounded-xl p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">الاسم (عربي)</label>
                <input type="text" name="name_ar" value="{{ old('name_ar', $product->name_ar) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">الاسم (إنجليزي)</label>
                <input type="text" name="name" required value="{{ old('name', $product->name) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">SKU</label>
                <input type="text" name="sku" required value="{{ old('sku', $product->sku) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">السعر</label>
                <input type="number" name="base_price" required step="0.01" value="{{ old('base_price', $product->base_price) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">سعر الخصم</label>
                <input type="number" name="sale_price" step="0.01" value="{{ old('sale_price', $product->sale_price) }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">الوصف (عربي)</label>
                <textarea name="description_ar" rows="2" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red resize-none">{{ old('description_ar', $product->description_ar) }}</textarea>
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">الجنس</label>
                <div class="flex flex-wrap gap-2">
                    @php $genders = is_array($product->gender) ? $product->gender : [$product->gender]; @endphp
                    @foreach(['men' => 'رجالي', 'women' => 'حريمي', 'boys' => 'أولادي', 'girls' => 'بناتي', 'kids' => 'أطفالي', 'unisex' => 'يونيسكس'] as $val => $label)
                        <label class="gender-label flex items-center gap-2 px-3 py-2 rounded-lg border border-white/10 cursor-pointer hover:border-brand-red transition-colors text-sm">
                            <input type="checkbox" name="gender[]" value="{{ $val }}" class="hidden peer" {{ in_array($val, $genders) ? 'checked' : '' }}>
                            <span class="text-white/70 peer-checked:text-brand-red peer-checked:font-bold">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Variants / Stock --}}
    @if($product->variants->count())
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-4">المخزون والتشكيلات</h2>
        @php
            $groupedVariants = $product->variants->groupBy('color');
        @endphp
        @foreach($groupedVariants as $color => $colorVariants)
            <div class="mb-4 last:mb-0">
                <div class="flex items-center gap-2 mb-2">
                    @php $hex = $colorVariants->first()->color_hex; @endphp
                    @if($hex)
                        <span class="w-4 h-4 rounded-full border border-white/20" style="background:{{ $hex }}"></span>
                    @endif
                    <span class="text-white/80 text-sm font-medium">{{ $color }}</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    @foreach($colorVariants as $variant)
                        <div class="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2">
                            <span class="text-white/60 text-sm font-medium w-10">{{ $variant->size }}</span>
                            <input type="number" name="variant_qty[{{ $variant->id }}]"
                                value="{{ $variant->quantity }}" min="0"
                                class="flex-1 bg-white/5 border border-white/10 rounded px-2 py-1 text-white text-sm text-center focus:outline-none focus:border-brand-red w-16" dir="ltr">
                            @if($variant->quantity <= 0)
                                <span class="w-2 h-2 rounded-full bg-red-500" title="نفذ"></span>
                            @elseif($variant->quantity <= 5)
                                <span class="w-2 h-2 rounded-full bg-yellow-500" title="منخفض"></span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-green-500" title="متوفر"></span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- Images grouped by color --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-4">الصور</h2>
        @php
            $productColors = $product->variants->pluck('color', 'color_hex')->unique();
            $imagesByColor = $product->images->groupBy('color_name');
        @endphp

        @foreach($productColors as $hex => $colorName)
            <div class="mb-5 last:mb-0 border border-white/10 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-5 h-5 rounded-full border border-white/20" style="background:{{ $hex }}"></span>
                    <span class="text-white font-medium text-sm">{{ $colorName }}</span>
                </div>

                {{-- Existing images for this color --}}
                @if(isset($imagesByColor[$colorName]) && $imagesByColor[$colorName]->count())
                    <div class="grid grid-cols-4 gap-2 mb-3">
                        @foreach($imagesByColor[$colorName] as $image)
                            <div class="relative rounded-lg overflow-hidden border border-white/10">
                                <img src="{{ $image->image_url }}" class="w-full aspect-square object-cover">
                                <label class="absolute top-1 right-1 flex items-center gap-1 bg-black/60 rounded-full px-1.5 py-0.5 cursor-pointer">
                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}">
                                    <span class="text-red-400 text-[9px]">حذف</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Upload more for this color --}}
                <div class="border border-dashed border-white/15 rounded-lg p-3 text-center cursor-pointer hover:border-brand-red transition-colors"
                     onclick="this.nextElementSibling.click()">
                    <p class="text-white/30 text-xs">+ إضافة صور {{ $colorName }}</p>
                </div>
                <input type="file" name="color_images[{{ $colorName }}][]" multiple accept="image/jpeg,image/png,image/webp" class="hidden"
                       onchange="previewInline(this)">
                <div class="previews grid grid-cols-4 gap-2 mt-2"></div>
            </div>
        @endforeach

        {{-- Images without color --}}
        @if(isset($imagesByColor['']) && $imagesByColor['']->count() || isset($imagesByColor[null]))
            @php $untagged = collect()->merge($imagesByColor[''] ?? [])->merge($imagesByColor[null] ?? []); @endphp
            @if($untagged->count())
            <div class="mt-4 border border-white/10 rounded-xl p-4">
                <p class="text-white/50 text-sm mb-3">صور بدون لون محدد</p>
                <div class="grid grid-cols-4 gap-2">
                    @foreach($untagged as $image)
                        <div class="relative rounded-lg overflow-hidden border border-white/10">
                            <img src="{{ $image->image_url }}" class="w-full aspect-square object-cover">
                            <label class="absolute top-1 right-1 flex items-center gap-1 bg-black/60 rounded-full px-1.5 py-0.5 cursor-pointer">
                                <input type="checkbox" name="delete_images[]" value="{{ $image->id }}">
                                <span class="text-red-400 text-[9px]">حذف</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endif
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

@push('styles')
<style>
    .gender-label:has(input:checked) { border-color: #e63946; background: rgba(230,57,70,0.1); }
</style>
@endpush

@push('scripts')
<script>
function previewInline(input) {
    const container = input.nextElementSibling;
    container.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'rounded-lg overflow-hidden border border-white/10';
            div.innerHTML = `<img src="${e.target.result}" class="w-full aspect-square object-cover">`;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endpush
