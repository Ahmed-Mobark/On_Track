@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Images --}}
        <div class="space-y-4">
            <div class="aspect-square bg-brand-dark rounded-xl overflow-hidden relative">
                @auth
                @php $inWishlist = auth()->user()->wishlistItems()->where('product_id', $product->id)->exists(); @endphp
                <button type="button" data-wishlist-id="{{ $product->id }}"
                    data-in-wishlist="{{ $inWishlist ? '1' : '0' }}"
                    class="absolute top-3 left-3 z-10"
                    style="width:40px;height:40px;border-radius:50%;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.3s;"
                    title="{{ $inWishlist ? 'إزالة من المفضلة' : 'أضف للمفضلة' }}">
                    <svg style="width:20px;height:20px;color:{{ $inWishlist ? '#e63946' : 'rgba(255,255,255,0.7)' }};" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
                @endauth
                @if($product->images->count())
                    @php $primaryImage = $product->images->sortBy('sort_order')->first(); @endphp
                    <img id="main-image" src="{{ $primaryImage->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white/20" id="main-image-placeholder">
                        <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <img src="" alt="" class="w-full h-full object-cover hidden" id="main-image">
                @endif
            </div>
            {{-- Thumbnails --}}
            <div class="grid grid-cols-5 gap-2" id="thumbnails">
                @foreach($product->images->sortBy('sort_order') as $image)
                    <button type="button" onclick="setMainImage('{{ $image->image_url }}')"
                        class="thumb-btn aspect-square bg-brand-dark rounded-lg overflow-hidden border border-white/10 hover:border-brand-red transition-colors {{ $loop->first ? 'ring-2 ring-brand-red/50' : '' }}"
                        data-url="{{ $image->image_url }}">
                        <img src="{{ $image->image_url }}" alt="" class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Details --}}
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $product->name_ar ?? $product->name }}</h1>

            <div class="flex items-center gap-3 mb-2" id="price-display">
                @if($product->sale_price)
                    <span class="text-2xl font-bold text-brand-red" id="current-price">{{ number_format($product->sale_price) }} ج.م</span>
                    <span class="text-white/40 text-lg line-through" id="old-price">{{ number_format($product->base_price) }} ج.م</span>
                @else
                    <span class="text-2xl font-bold text-white" id="current-price">{{ number_format($product->base_price) }} ج.م</span>
                    <span class="text-white/40 text-lg line-through hidden" id="old-price"></span>
                @endif
            </div>

            {{-- Stock indicator --}}
            <p class="text-sm mb-6 hidden" id="stock-info">
                <span id="stock-text"></span>
            </p>

            @if($product->description_ar ?? $product->description)
                <p class="text-white/60 mb-6">{{ $product->description_ar ?? $product->description }}</p>
            @endif

            @php
                $hasVariants = $product->variants->count() > 0;
                $inStockVariants = $hasVariants ? $product->variants->where('quantity', '>', 0) : collect();
                $isAvailable = $inStockVariants->count() > 0;
            @endphp

            @if(!$hasVariants || !$isAvailable)
                {{-- No variants or all out of stock --}}
                <div style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.15);border-radius:12px;padding:24px;text-align:center;margin-bottom:16px;">
                    <svg style="width:32px;height:32px;margin:0 auto 12px;color:rgba(239,68,68,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    <p style="color:#f87171;font-weight:700;font-size:16px;margin-bottom:4px;">غير متوفر حالياً</p>
                    <p style="color:rgba(255,255,255,0.3);font-size:13px;">هذا المنتج غير متوفر. تواصل معنا للاستفسار</p>
                </div>
                <a href="https://wa.me/201010300353?text={{ urlencode('مرحباً، أستفسر عن توفر المنتج: ' . ($product->name_ar ?? $product->name) . ' - ' . $product->sku) }}"
                   target="_blank"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;background:#22c55e;color:white;font-weight:700;padding:16px;border-radius:12px;font-size:16px;transition:all 0.3s;"
                   onmouseover="this.style.background='#16a34a'" onmouseout="this.style.background='#22c55e'">
                    <svg style="width:20px;height:20px;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    استفسر عبر واتساب
                </a>
            @else
                {{-- Has in-stock variants --}}
                <form action="{{ route('cart.add') }}" method="POST" class="space-y-4" id="add-to-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    {{-- Size --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-white/70 text-sm font-medium">المقاس</label>
                            <button type="button" onclick="document.getElementById('size-guide-modal').classList.remove('hidden')"
                                class="flex items-center gap-1 text-xs text-brand-red hover:text-brand-red-dark transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                دليل المقاسات
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2" id="size-options"></div>
                    </div>

                    {{-- Color --}}
                    <div>
                        <label class="block text-white/70 text-sm font-medium mb-2">اللون</label>
                        <div class="flex flex-wrap gap-2" id="color-options"></div>
                    </div>

                    <input type="hidden" name="variant_id" id="variant_id" required>

                    <div>
                        <label class="block text-white/70 text-sm font-medium mb-2">الكمية</label>
                        <div dir="ltr" style="display:inline-flex;align-items:center;border:1px solid rgba(255,255,255,0.15);border-radius:10px;overflow:hidden;">
                            <button type="button" onclick="changeQty(-1)"
                                style="width:44px;height:44px;background:rgba(255,255,255,0.05);border:none;color:white;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">−</button>
                            <span id="quantity-display" style="width:52px;height:44px;display:flex;align-items:center;justify-content:center;color:white;font-size:18px;font-weight:700;border-right:1px solid rgba(255,255,255,0.1);border-left:1px solid rgba(255,255,255,0.1);">1</span>
                            <input type="hidden" name="quantity" value="1" id="quantity-input">
                            <button type="button" onclick="changeQty(1)"
                                style="width:44px;height:44px;background:rgba(255,255,255,0.05);border:none;color:white;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">+</button>
                        </div>
                    </div>

                    <button type="submit" id="add-btn" disabled
                        style="width:100%;background:#e63946;color:white;font-weight:700;padding:16px;border-radius:12px;font-size:16px;border:none;cursor:not-allowed;opacity:0.4;transition:all 0.3s;">
                        اختار المقاس واللون
                    </button>
                </form>
            @endif


            {{-- Share button --}}
            <div class="mt-6">
                <button type="button" onclick="shareProduct()" id="share-btn"
                    style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;border:1px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.7);font-size:14px;font-weight:600;cursor:pointer;transition:all 0.3s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='white'" onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.color='rgba(255,255,255,0.7)'">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    <span id="share-text">مشاركة المنتج</span>
                </button>
            </div>

            {{-- Product info --}}
            <div class="mt-8 pt-6 border-t border-white/10 space-y-3 text-sm">
                <p class="text-white/50">SKU: <span class="text-white/70" id="variant-sku">{{ $product->sku }}</span></p>
                @if($product->materials)
                    <p class="text-white/50">الخامات: <span class="text-white/70">{{ $product->materials }}</span></p>
                @endif
                @if($product->care_instructions)
                    <p class="text-white/50">العناية: <span class="text-white/70">{{ $product->care_instructions }}</span></p>
                @endif
            </div>
        </div>
    </div>

    {{-- Reviews --}}
    @if($product->reviews->count())
    <section class="mt-16 pt-8 border-t border-white/10">
        <h2 class="text-xl font-bold text-white mb-6">التقييمات ({{ $product->total_reviews }})</h2>
        <div class="space-y-4">
            @foreach($product->reviews as $review)
                <div class="bg-brand-dark rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-white font-medium text-sm">{{ $review->user->first_name }}</span>
                        <span class="text-yellow-400 text-sm">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                    </div>
                    @if($review->comment)
                        <p class="text-white/60 text-sm">{{ $review->comment }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Related Products --}}
    @if(isset($relatedProducts) && $relatedProducts->count())
    <section class="mt-16 pt-8 border-t border-white/10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">منتجات <span style="color:#e63946;">ذات صلة</span></h2>
            <div class="flex gap-2">
                {{-- Right arrow (scrolls right in RTL = negative left) --}}
                <button onclick="document.getElementById('related-scroll').scrollBy({left:-300,behavior:'smooth'})"
                    style="width:36px;height:36px;border-radius:50%;border:1px solid rgba(255,255,255,0.1);background:transparent;color:rgba(255,255,255,0.5);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='white'" onmouseout="this.style.background='transparent';this.style.color='rgba(255,255,255,0.5)'">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                {{-- Left arrow (scrolls left in RTL = positive left) --}}
                <button onclick="document.getElementById('related-scroll').scrollBy({left:300,behavior:'smooth'})"
                    style="width:36px;height:36px;border-radius:50%;border:1px solid rgba(255,255,255,0.1);background:transparent;color:rgba(255,255,255,0.5);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='white'" onmouseout="this.style.background='transparent';this.style.color='rgba(255,255,255,0.5)'">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
            </div>
        </div>
        <div id="related-scroll" style="display:flex;gap:16px;overflow-x:auto;padding-bottom:16px;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;">
            @foreach($relatedProducts as $relProduct)
                <div style="min-width:200px;max-width:200px;scroll-snap-align:start;flex-shrink:0;">
                    @include('components.product-card', ['product' => $relProduct])
                </div>
            @endforeach
        </div>
    </section>
    @endif
</div>


{{-- Size Guide Modal --}}
<div id="size-guide-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="document.getElementById('size-guide-modal').classList.add('hidden')"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-brand-dark rounded-2xl border border-white/10 w-full max-w-2xl relative max-h-[90vh] overflow-y-auto">
            {{-- Header --}}
            <div class="sticky top-0 bg-brand-dark border-b border-white/10 px-6 py-4 flex items-center justify-between z-10 rounded-t-2xl">
                <h2 class="text-lg font-bold text-white">دليل المقاسات</h2>
                <button onclick="document.getElementById('size-guide-modal').classList.add('hidden')" class="text-white/40 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6">
                {{-- Unit Toggle --}}
                <div class="flex items-center justify-between mb-6">
                    <p class="text-white/50 text-sm">كل القياسات بالسنتيمتر (CM)</p>
                    <div class="flex items-center gap-2 bg-white/5 rounded-full p-1">
                        <button onclick="toggleUnit('cm')" id="unit-cm" class="px-3 py-1 rounded-full text-xs font-bold bg-white text-black transition-all">CM</button>
                        <button onclick="toggleUnit('in')" id="unit-in" class="px-3 py-1 rounded-full text-xs font-bold text-white/50 transition-all">IN</button>
                    </div>
                </div>

                {{-- Body Illustration + How to measure --}}
                <div class="bg-white/[0.03] rounded-xl p-4 mb-6">
                    <h3 class="text-sm font-semibold text-white mb-3">ازاي تاخد مقاساتك</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-white/60">
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-brand-red/20 text-brand-red text-xs flex items-center justify-center shrink-0 mt-0.5 font-bold">1</span>
                            <div>
                                <span class="text-white/80 font-medium">محيط الصدر</span>
                                <p class="text-xs mt-0.5">قيس حوالين أعرض منطقة في الصدر تحت الإبط</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-brand-red/20 text-brand-red text-xs flex items-center justify-center shrink-0 mt-0.5 font-bold">2</span>
                            <div>
                                <span class="text-white/80 font-medium">محيط الوسط</span>
                                <p class="text-xs mt-0.5">قيس حوالين أنحف منطقة في الوسط</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-brand-red/20 text-brand-red text-xs flex items-center justify-center shrink-0 mt-0.5 font-bold">3</span>
                            <div>
                                <span class="text-white/80 font-medium">الطول</span>
                                <p class="text-xs mt-0.5">طولك من الرأس للقدم واقف مستقيم</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-brand-red/20 text-brand-red text-xs flex items-center justify-center shrink-0 mt-0.5 font-bold">4</span>
                            <div>
                                <span class="text-white/80 font-medium">الوزن</span>
                                <p class="text-xs mt-0.5">وزنك التقريبي بالكيلوجرام</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Size Table - Tops (T-shirts, Hoodies, Jackets) --}}
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        تيشيرتات / هوديز / جاكيتات
                    </h3>
                    <div class="overflow-x-auto rounded-lg border border-white/10">
                        <table class="w-full text-sm" id="tops-table">
                            <thead>
                                <tr class="bg-white/5">
                                    <th class="px-3 py-2.5 text-right text-white/70 font-semibold border-b border-white/10">المقاس</th>
                                    <th class="px-3 py-2.5 text-center text-white/70 font-semibold border-b border-white/10">الصدر</th>
                                    <th class="px-3 py-2.5 text-center text-white/70 font-semibold border-b border-white/10">الوسط</th>
                                    <th class="px-3 py-2.5 text-center text-white/70 font-semibold border-b border-white/10">الطول</th>
                                    <th class="px-3 py-2.5 text-center text-white/70 font-semibold border-b border-white/10">الوزن</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-3 py-2.5 font-bold text-white">S</td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="88-92">88-92</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="73-77">73-77</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="165-170">165-170</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60">55-65 kg</td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-3 py-2.5 font-bold text-white">M</td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="92-96">92-96</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="78-82">78-82</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="170-175">170-175</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60">65-75 kg</td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-3 py-2.5 font-bold text-white">L</td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="96-102">96-102</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="83-88">83-88</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="175-180">175-180</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60">75-85 kg</td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-3 py-2.5 font-bold text-white">XL</td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="102-108">102-108</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="89-95">89-95</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="178-185">178-185</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60">85-100 kg</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Size Table - Bottoms (Shorts, Pants) --}}
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        شورتات / بنطلونات
                    </h3>
                    <div class="overflow-x-auto rounded-lg border border-white/10">
                        <table class="w-full text-sm" id="bottoms-table">
                            <thead>
                                <tr class="bg-white/5">
                                    <th class="px-3 py-2.5 text-right text-white/70 font-semibold border-b border-white/10">المقاس</th>
                                    <th class="px-3 py-2.5 text-center text-white/70 font-semibold border-b border-white/10">الوسط</th>
                                    <th class="px-3 py-2.5 text-center text-white/70 font-semibold border-b border-white/10">الأرداف</th>
                                    <th class="px-3 py-2.5 text-center text-white/70 font-semibold border-b border-white/10">الطول</th>
                                    <th class="px-3 py-2.5 text-center text-white/70 font-semibold border-b border-white/10">الوزن</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-3 py-2.5 font-bold text-white">S</td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="73-77">73-77</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="90-94">90-94</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="165-170">165-170</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60">55-65 kg</td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-3 py-2.5 font-bold text-white">M</td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="78-82">78-82</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="95-99">95-99</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="170-175">170-175</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60">65-75 kg</td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-3 py-2.5 font-bold text-white">L</td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="83-88">83-88</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="100-104">100-104</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="175-180">175-180</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60">75-85 kg</td>
                                </tr>
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-3 py-2.5 font-bold text-white">XL</td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="89-95">89-95</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="105-110">105-110</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60"><span data-cm="178-185">178-185</span></td>
                                    <td class="px-3 py-2.5 text-center text-white/60">85-100 kg</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Quick Size Finder --}}
                <div class="bg-brand-red/5 border border-brand-red/20 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        اعرف مقاسك بسرعة
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-white/50 mb-1 block">الوزن (kg)</label>
                            <input type="number" id="sg-weight" placeholder="75" min="40" max="150"
                                   class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red placeholder-white/20">
                        </div>
                        <div>
                            <label class="text-xs text-white/50 mb-1 block">الطول (cm)</label>
                            <input type="number" id="sg-height" placeholder="175" min="150" max="210"
                                   class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red placeholder-white/20">
                        </div>
                    </div>
                    <button type="button" onclick="findMySize()"
                            class="mt-3 w-full bg-brand-red hover:bg-brand-red-dark text-white py-2.5 rounded-lg text-sm font-bold transition-colors">
                        اعرف مقاسك
                    </button>
                    <div id="sg-result" class="hidden mt-3 text-center">
                        <p class="text-white/60 text-sm">المقاس المناسب ليك:</p>
                        <p class="text-3xl font-black text-brand-red mt-1" id="sg-result-size"></p>
                        <p class="text-xs text-white/40 mt-1" id="sg-result-note"></p>
                    </div>
                </div>

                {{-- Tips --}}
                <div class="mt-4 text-xs text-white/30 space-y-1">
                    <p>* المقاسات تقريبية وممكن تختلف بفرق 1-2 سم</p>
                    <p>* لو بين مقاسين، ننصحك تاخد المقاس الأكبر للراحة أثناء التمرين</p>
                    <p>* الملابس الرياضية مصممة بقصة مريحة (Regular Fit)</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Size Guide - Unit toggle
    function toggleUnit(unit) {
        var cmBtn = document.getElementById('unit-cm');
        var inBtn = document.getElementById('unit-in');
        var spans = document.querySelectorAll('#size-guide-modal [data-cm]');

        if (unit === 'in') {
            cmBtn.className = 'px-3 py-1 rounded-full text-xs font-bold text-white/50 transition-all';
            inBtn.className = 'px-3 py-1 rounded-full text-xs font-bold bg-white text-black transition-all';
            spans.forEach(function(s) {
                var cm = s.getAttribute('data-cm');
                var parts = cm.split('-');
                var converted = parts.map(function(v) { return (parseFloat(v) / 2.54).toFixed(1); }).join('-');
                s.textContent = converted;
            });
        } else {
            cmBtn.className = 'px-3 py-1 rounded-full text-xs font-bold bg-white text-black transition-all';
            inBtn.className = 'px-3 py-1 rounded-full text-xs font-bold text-white/50 transition-all';
            spans.forEach(function(s) {
                s.textContent = s.getAttribute('data-cm');
            });
        }
    }

    // Quick size finder
    function findMySize() {
        var weight = parseFloat(document.getElementById('sg-weight').value);
        var height = parseFloat(document.getElementById('sg-height').value);
        if (!weight || !height) { showToast('ادخل الوزن والطول'); return; }

        var size = 'M';
        var note = '';

        if (weight <= 65 && height <= 170) {
            size = 'S';
            note = 'مقاس صغير - مناسب للجسم النحيف';
        } else if (weight <= 75 && height <= 178) {
            size = 'M';
            note = 'مقاس وسط - الأكثر شيوعاً';
        } else if (weight <= 88 && height <= 183) {
            size = 'L';
            note = 'مقاس كبير - مناسب للجسم الرياضي';
        } else {
            size = 'XL';
            note = 'مقاس كبير جداً - راحة إضافية';
        }

        // Edge cases
        if (weight > 95 && height < 175) {
            size = 'XL';
            note = 'ننصح بـ XL عشان الراحة في منطقة الوسط';
        }
        if (weight < 60 && height > 180) {
            size = 'M';
            note = 'M هيكون مناسب للطول مع الجسم النحيف';
        }

        document.getElementById('sg-result').classList.remove('hidden');
        document.getElementById('sg-result-size').textContent = size;
        document.getElementById('sg-result-note').textContent = note;
    }
</script>

<script>
    function shareProduct() {
        const shareData = {
            title: @json($product->name_ar ?? $product->name),
            url: window.location.href
        };
        if (navigator.share) {
            navigator.share(shareData).catch(() => {});
        } else {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const txt = document.getElementById('share-text');
                txt.textContent = 'تم نسخ الرابط!';
                setTimeout(() => { txt.textContent = 'مشاركة المنتج'; }, 2000);
            });
        }
    }
</script>
@if($hasVariants && $isAvailable)
<script>
    function changeQty(delta) {
        const inp = document.getElementById('quantity-input');
        const display = document.getElementById('quantity-display');
        let v = parseInt(inp.value) + delta;
        if (v < 1) v = 1;
        if (v > 10) v = 10;
        inp.value = v;
        display.textContent = v;
    }

    const variants = @json($product->variants);
    const images = @json($product->images->sortBy('sort_order')->values());
    let selectedSize = null;
    let selectedColor = null;

    function imageUrl(img) {
        if (!img) return '';
        if (img.image_url) return img.image_url;
        if (img.url && String(img.url).startsWith('http')) return img.url;
        return '/media/' + String(img.url || '').replace(/^\//, '');
    }

    function setMainImage(url) {
        const mainImg = document.getElementById('main-image');
        const placeholder = document.getElementById('main-image-placeholder');
        if (!mainImg || !url) return;
        mainImg.src = url;
        mainImg.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');
    }

    function sortImages(list) {
        return list.slice().sort(function (a, b) { return (a.sort_order || 0) - (b.sort_order || 0); });
    }

    const allSizes = [...new Set(variants.map(v => v.size))];
    const allColors = [...new Set(variants.map(v => v.color))];
    const colorHexMap = {};
    variants.forEach(v => { if (v.color_hex) colorHexMap[v.color] = v.color_hex; });

    // Initial render
    renderSizes(allSizes);
    renderColors(allColors);

    function renderSizes(availableSizes) {
        const c = document.getElementById('size-options');
        c.innerHTML = '';
        allSizes.forEach(size => {
            const avail = availableSizes.includes(size);
            const sel = selectedSize === size;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = size;
            btn.style.cssText = `
                padding: 8px 18px; border-radius: 8px; font-size: 14px;
                cursor: ${avail ? 'pointer' : 'not-allowed'};
                border: 1px solid ${sel ? '#e63946' : (avail ? 'rgba(255,255,255,0.15)' : 'rgba(255,255,255,0.05)')};
                color: ${sel ? '#e63946' : (avail ? 'rgba(255,255,255,0.7)' : 'rgba(255,255,255,0.2)')};
                background: ${sel ? 'rgba(230,57,70,0.1)' : 'transparent'};
                font-weight: ${sel ? 'bold' : 'normal'};
                ${!avail ? 'text-decoration: line-through;' : ''}
                transition: all 0.2s;
            `;
            if (avail) btn.onclick = () => selectSize(size);
            c.appendChild(btn);
        });
    }

    function renderColors(availableColors) {
        const c = document.getElementById('color-options');
        c.innerHTML = '';
        allColors.forEach(color => {
            const avail = availableColors.includes(color);
            const sel = selectedColor === color;
            const hex = colorHexMap[color];
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.style.cssText = `
                padding: 8px 18px; border-radius: 8px; font-size: 14px;
                display: flex; align-items: center; gap: 8px;
                cursor: ${avail ? 'pointer' : 'not-allowed'};
                border: 1px solid ${sel ? '#e63946' : (avail ? 'rgba(255,255,255,0.15)' : 'rgba(255,255,255,0.05)')};
                color: ${sel ? '#e63946' : (avail ? 'rgba(255,255,255,0.7)' : 'rgba(255,255,255,0.2)')};
                background: ${sel ? 'rgba(230,57,70,0.1)' : 'transparent'};
                font-weight: ${sel ? 'bold' : 'normal'};
                ${!avail ? 'text-decoration: line-through;' : ''}
                transition: all 0.2s;
            `;
            if (hex) {
                const dot = document.createElement('span');
                dot.style.cssText = `width:16px;height:16px;border-radius:50%;border:1px solid rgba(255,255,255,0.2);background:${hex};opacity:${avail?1:0.3};display:inline-block;`;
                btn.appendChild(dot);
            }
            btn.appendChild(document.createTextNode(color));
            if (avail) btn.onclick = () => selectColor(color);
            c.appendChild(btn);
        });
    }

    function selectSize(size) {
        selectedSize = size;
        const availableColors = [...new Set(variants.filter(v => v.size === size && v.quantity > 0).map(v => v.color))];
        if (selectedColor && !availableColors.includes(selectedColor)) selectedColor = null;
        renderSizes(allSizes);
        renderColors(availableColors.length ? availableColors : allColors);
        updateVariant();
    }

    function selectColor(color) {
        selectedColor = color;
        const availableSizes = [...new Set(variants.filter(v => v.color === color && v.quantity > 0).map(v => v.size))];
        if (selectedSize && !availableSizes.includes(selectedSize)) selectedSize = null;
        renderColors(allColors);
        renderSizes(availableSizes.length ? availableSizes : allSizes);

        // Update images for this color (fallback to all images if none for this color)
        let colorImgs = sortImages(images.filter(img => img.color_name === color));
        if (colorImgs.length === 0) colorImgs = sortImages(images);
        if (colorImgs.length > 0) {
            setMainImage(imageUrl(colorImgs[0]));

            // Update thumbnails
            const thumbs = document.getElementById('thumbnails');
            thumbs.innerHTML = '';
            colorImgs.forEach(function (img, index) {
                const btn = document.createElement('button');
                btn.type = 'button';
                const url = imageUrl(img);
                btn.className = 'thumb-btn aspect-square bg-brand-dark rounded-lg overflow-hidden border border-white/10 hover:border-brand-red transition-colors' + (index === 0 ? ' ring-2 ring-brand-red/50' : '');
                btn.onclick = function () { setMainImage(url); };
                btn.innerHTML = '<img src="' + url + '" alt="" class="w-full h-full object-cover">';
                thumbs.appendChild(btn);
            });
        }

        updateVariant();
    }

    function updateVariant() {
        const addBtn = document.getElementById('add-btn');
        const variantInput = document.getElementById('variant_id');
        const stockInfo = document.getElementById('stock-info');
        const stockText = document.getElementById('stock-text');
        const skuDisplay = document.getElementById('variant-sku');

        if (selectedSize && selectedColor) {
            const variant = variants.find(v => v.size === selectedSize && v.color === selectedColor);
            if (variant) {
                variantInput.value = variant.id;
                skuDisplay.textContent = variant.sku;
                if (variant.price) {
                    document.getElementById('current-price').textContent = Number(variant.price).toLocaleString() + ' ج.م';
                }
                stockInfo.classList.remove('hidden');
                if (variant.quantity > 0) {
                    stockText.textContent = variant.quantity <= 5 ? 'باقي ' + variant.quantity + ' قطع فقط!' : 'متوفر';
                    stockText.style.color = variant.quantity <= 5 ? '#facc15' : '#4ade80';
                    addBtn.disabled = false;
                    addBtn.textContent = 'أضف للسلة';
                    addBtn.style.opacity = '1';
                    addBtn.style.cursor = 'pointer';
                } else {
                    stockText.textContent = 'غير متوفر';
                    stockText.style.color = '#f87171';
                    addBtn.disabled = true;
                    addBtn.textContent = 'غير متوفر';
                    addBtn.style.opacity = '0.4';
                    addBtn.style.cursor = 'not-allowed';
                }
            } else {
                addBtn.disabled = true;
                addBtn.textContent = 'التشكيلة غير متوفرة';
                addBtn.style.opacity = '0.4';
                stockInfo.classList.add('hidden');
            }
        } else {
            addBtn.disabled = true;
            addBtn.textContent = 'اختار المقاس واللون';
            addBtn.style.opacity = '0.4';
            addBtn.style.cursor = 'not-allowed';
            stockInfo.classList.add('hidden');
        }
    }
</script>
@endif

@endpush
@endsection
