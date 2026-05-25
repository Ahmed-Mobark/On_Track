@extends('layouts.admin')
@section('title', 'إضافة منتج')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">إضافة منتج جديد</h1>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-3xl">
    @csrf

    {{-- Basic Info --}}
    <div class="bg-brand-dark rounded-xl p-6 space-y-4">
        <h2 class="text-white font-bold mb-2">البيانات الأساسية</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">الاسم (عربي) *</label>
                <input type="text" name="name_ar" required value="{{ old('name_ar') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">الاسم (إنجليزي) *</label>
                <input type="text" name="name" required value="{{ old('name') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-white/70 text-sm mb-1">SKU *</label>
                <input type="text" name="sku" required value="{{ old('sku') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">السعر *</label>
                <input type="number" name="base_price" required step="0.01" value="{{ old('base_price') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">سعر الخصم</label>
                <input type="number" name="sale_price" step="0.01" value="{{ old('sale_price') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
        </div>
        <div>
            <label class="block text-white/70 text-sm mb-1">الوصف (عربي)</label>
            <textarea name="description_ar" rows="2" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red resize-none">{{ old('description_ar') }}</textarea>
        </div>
    </div>

    {{-- Colors --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-white font-bold mb-1">الألوان والمقاسات والصور *</h2>
        <p class="text-white/30 text-xs mb-4">اضغط على لون لإضافته — لكل لون هتحدد صوره ومقاساته وكميته</p>

        <div class="flex flex-wrap gap-2 mb-4" id="color-buttons"></div>

        <div id="no-colors-msg" class="border-2 border-dashed border-white/10 rounded-xl p-6 text-center">
            <p class="text-white/30 text-sm">اضغط على لون من فوق لإضافته</p>
        </div>

        <div id="color-sections" class="space-y-4"></div>
    </div>

    {{-- Categories + Gender --}}
    <div class="bg-brand-dark rounded-xl p-6 space-y-4">
        <div>
            <h2 class="text-white font-bold mb-3">التصنيف</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $category)
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-white/10 cursor-pointer hover:border-brand-red transition-colors text-sm">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                        <span class="text-white/70">{{ $category->name_ar ?? $category->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div>
            <label class="block text-white/70 text-sm mb-1">الجنس</label>
            <div class="flex flex-wrap gap-2">
                @foreach(['men' => 'رجالي', 'women' => 'حريمي', 'boys' => 'أولادي', 'girls' => 'بناتي', 'kids' => 'أطفالي', 'unisex' => 'يونيسكس'] as $val => $label)
                    <label class="gender-label flex items-center gap-2 px-3 py-2 rounded-lg border border-white/10 cursor-pointer hover:border-brand-red transition-colors text-sm">
                        <input type="checkbox" name="gender[]" value="{{ $val }}" class="hidden peer">
                        <span class="text-white/70 peer-checked:text-brand-red peer-checked:font-bold">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Options --}}
    <div class="bg-brand-dark rounded-xl p-6">
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

    <button type="submit" class="bg-brand-red hover:bg-brand-red-dark text-white px-8 py-3 rounded-xl font-semibold transition-colors">
        حفظ المنتج
    </button>
</form>
@endsection

@push('styles')
<style>
    .size-label:has(input:checked) { border-color: #e63946; background: rgba(230,57,70,0.1); }
    .gender-label:has(input:checked) { border-color: #e63946; background: rgba(230,57,70,0.1); }
    select option { background: #141414; color: white; }
</style>
@endpush

@push('scripts')
<script>
const allColors = [
    @foreach([
        ['name' => 'أسود', 'hex' => '#000000'], ['name' => 'أبيض', 'hex' => '#FFFFFF'],
        ['name' => 'رمادي', 'hex' => '#6B7280'], ['name' => 'كحلي', 'hex' => '#1E3A5F'],
        ['name' => 'أحمر', 'hex' => '#DC2626'], ['name' => 'أزرق', 'hex' => '#2563EB'],
        ['name' => 'أخضر', 'hex' => '#16A34A'], ['name' => 'بني', 'hex' => '#92400E'],
        ['name' => 'بيج', 'hex' => '#D4B896'], ['name' => 'زيتي', 'hex' => '#556B2F'],
        ['name' => 'عنابي', 'hex' => '#800020'], ['name' => 'وردي', 'hex' => '#EC4899'],
        ['name' => 'برتقالي', 'hex' => '#EA580C'], ['name' => 'أصفر', 'hex' => '#EAB308'],
        ['name' => 'بنفسجي', 'hex' => '#7C3AED'], ['name' => 'تركواز', 'hex' => '#06B6D4'],
    ] as $c)
    { name: '{{ $c['name'] }}', hex: '{{ $c['hex'] }}' },
    @endforeach
];

const addedColors = new Set();
const letterSizes = ['XS','S','M','L','XL','XXL','3XL'];
const numberSizes = [6,8,10,12,14,16];

// Build color buttons
renderColorButtons();

function renderColorButtons() {
    const container = document.getElementById('color-buttons');
    container.innerHTML = '';
    allColors.forEach(c => {
        if (addedColors.has(c.name)) return;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'flex items-center gap-2 px-3 py-2 rounded-lg border border-white/10 cursor-pointer hover:border-brand-red transition-colors text-sm';
        btn.innerHTML = `<span class="w-5 h-5 rounded-full border border-white/20" style="background:${c.hex}"></span><span class="text-white/70">${c.name}</span>`;
        btn.onclick = () => addColor(c.name);
        container.appendChild(btn);
    });
}

function addColor(colorName) {
    if (addedColors.has(colorName)) return;
    const colorObj = allColors.find(c => c.name === colorName);
    if (!colorObj) return;

    addedColors.add(colorName);
    renderColorButtons();
    document.getElementById('no-colors-msg').classList.add('hidden');
    buildColorSection(colorObj.name, colorObj.hex);
}

function removeColor(colorName) {
    const section = document.querySelector(`[data-color-section="${colorName}"]`);
    if (section) section.remove();
    addedColors.delete(colorName);
    renderColorButtons();

    if (addedColors.size === 0) {
        document.getElementById('no-colors-msg').classList.remove('hidden');
    }
}

function buildColorSection(color, hex) {
    const container = document.getElementById('color-sections');

    let sizesHtml = '<p class="text-white/50 text-xs mb-1.5">حروف</p><div class="flex flex-wrap gap-1.5 mb-2">';
    letterSizes.forEach(s => {
        sizesHtml += `<label class="size-label flex items-center justify-center w-12 h-8 rounded-lg border border-white/10 cursor-pointer hover:border-brand-red transition-colors text-xs text-white/70">
            <input type="checkbox" name="variants[${color}][sizes][]" value="${s}" class="hidden peer">
            <span class="peer-checked:text-brand-red peer-checked:font-bold">${s}</span>
        </label>`;
    });
    sizesHtml += '</div><p class="text-white/50 text-xs mb-1.5">أرقام</p><div class="flex flex-wrap gap-1.5">';
    numberSizes.forEach(s => {
        sizesHtml += `<label class="size-label flex items-center justify-center w-10 h-8 rounded-lg border border-white/10 cursor-pointer hover:border-brand-red transition-colors text-xs text-white/70">
            <input type="checkbox" name="variants[${color}][sizes][]" value="${s}" class="hidden peer">
            <span class="peer-checked:text-brand-red peer-checked:font-bold">${s}</span>
        </label>`;
    });
    sizesHtml += '</div>';

    const section = document.createElement('div');
    section.dataset.colorSection = color;
    section.className = 'bg-brand-dark rounded-xl p-5 border border-white/10';
    section.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full border-2 border-white/20" style="background:${hex}"></span>
                <span class="text-white font-bold">${color}</span>
            </div>
            <button type="button" onclick="removeColor('${color}')" class="text-red-400 text-xs hover:underline">حذف اللون</button>
        </div>

        <div class="space-y-4">
            <div>
                <p class="text-white/70 text-sm font-medium mb-2">الصور</p>
                <div class="upload-area border-2 border-dashed border-white/15 rounded-lg p-4 text-center cursor-pointer hover:border-brand-red transition-colors"
                     onclick="this.nextElementSibling.click()">
                    <p class="text-white/30 text-sm">+ ارفع صور ${color}</p>
                </div>
                <input type="file" name="color_images[${color}][]" multiple accept="image/jpeg,image/png,image/webp" class="hidden"
                       onchange="previewColorImages(this)">
                <div class="previews grid grid-cols-4 gap-2 mt-2"></div>
            </div>

            <div>
                <p class="text-white/70 text-sm font-medium mb-2">المقاسات المتاحة لـ ${color}</p>
                ${sizesHtml}
            </div>

            <div class="flex items-center gap-2">
                <label class="text-white/70 text-sm">الكمية لكل مقاس</label>
                <input type="number" name="variants[${color}][quantity]" value="10" min="0"
                    class="w-24 bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
        </div>
    `;
    container.appendChild(section);
}

function previewColorImages(input) {
    const container = input.nextElementSibling;
    container.innerHTML = '';
    Array.from(input.files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative rounded-lg overflow-hidden border border-white/10';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full aspect-square object-cover">
                ${i === 0 ? '<span class="absolute top-1 right-1 bg-brand-red text-white text-[8px] px-1.5 py-0.5 rounded-full">رئيسية</span>' : ''}
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endpush
