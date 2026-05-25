@extends('layouts.admin')
@section('title', 'العروض والبانرات')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">العروض والبانرات</h1>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Add Form --}}
    <div class="bg-brand-dark rounded-xl p-6 border border-white/5 h-fit">
        <h2 class="text-white font-bold mb-4">إضافة بانر جديد</h2>
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-white/70 text-xs mb-1">الصورة *</label>
                <input type="file" name="image" required accept="image/jpeg,image/png,image/webp"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-xs focus:outline-none">
                <p class="text-white/30 text-[10px] mt-1">1200×500 أو أكبر — JPG, PNG, WebP</p>
            </div>
            <div>
                <label class="block text-white/70 text-xs mb-1">العنوان</label>
                <input type="text" name="title" placeholder="مثال: خصم 30% على كل المنتجات"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
            <div>
                <label class="block text-white/70 text-xs mb-1">العنوان الفرعي</label>
                <input type="text" name="subtitle" placeholder="لفترة محدودة"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
            <div>
                <label class="block text-white/70 text-xs mb-1">الرابط (لما يضغط على البانر)</label>
                <input type="text" name="link" placeholder="/products?category=hoodies أو رابط منتج" dir="ltr"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
            <div>
                <label class="block text-white/70 text-xs mb-1">نص الزر</label>
                <input type="text" name="button_text" placeholder="تسوق الآن"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
            </div>
            <button type="submit" class="w-full bg-brand-red hover:bg-brand-red-dark text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                إضافة البانر
            </button>
        </form>
    </div>

    {{-- Banners List --}}
    <div class="lg:col-span-2 space-y-4">
        @forelse($banners as $banner)
            <div class="bg-brand-dark rounded-xl border border-white/5 overflow-hidden">
                {{-- Preview --}}
                <div class="relative aspect-[2.4/1] bg-white/5">
                    <img src="{{ asset('storage/' . $banner->image) }}" class="w-full h-full object-cover">
                    @if(!$banner->is_active)
                        <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                            <span class="text-white/60 text-sm font-bold">غير نشط</span>
                        </div>
                    @endif
                    @if($banner->title)
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                            <p class="text-white font-bold">{{ $banner->title }}</p>
                            @if($banner->subtitle)
                                <p class="text-white/60 text-sm">{{ $banner->subtitle }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Edit Form --}}
                <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="p-4">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <input type="text" name="title" value="{{ $banner->title }}" placeholder="العنوان"
                            class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
                        <input type="text" name="subtitle" value="{{ $banner->subtitle }}" placeholder="العنوان الفرعي"
                            class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <input type="text" name="link" value="{{ $banner->link }}" placeholder="الرابط" dir="ltr"
                            class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
                        <input type="text" name="button_text" value="{{ $banner->button_text }}" placeholder="نص الزر"
                            class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
                    </div>
                    <div class="flex items-center gap-3 mb-3">
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                            class="flex-1 bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-white text-xs focus:outline-none">
                        <label class="flex items-center gap-2 text-white/70 text-sm whitespace-nowrap">
                            <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }}>
                            نشط
                        </label>
                    </div>
                    <button type="submit" class="w-full bg-brand-red hover:bg-brand-red-dark text-white py-2 rounded-lg text-sm font-medium transition-colors">
                        حفظ التعديلات
                    </button>
                </form>
                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('حذف البانر؟')" class="px-4 pb-4">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full bg-red-500/10 text-red-400 py-2 rounded-lg text-sm hover:bg-red-500/20 transition-colors">
                        حذف البانر
                    </button>
                </form>
            </div>
        @empty
            <div class="bg-brand-dark rounded-xl p-12 text-center border border-white/5">
                <p class="text-white/30">لا توجد بانرات. أضف أول بانر من النموذج</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
