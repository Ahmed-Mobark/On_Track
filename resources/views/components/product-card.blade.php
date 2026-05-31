@php
    $hasStock = $product->variants->where('quantity', '>', 0)->count() > 0;
@endphp
<div class="relative group">
    <a href="{{ route('product.show', $product->slug) }}">
        <div class="bg-brand-dark rounded-2xl overflow-hidden border border-white/[0.04] hover:border-white/10 product-hover">
            <div class="aspect-[3/4] bg-white/[0.02] relative overflow-hidden">
                @if($product->images->first())
                    <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}"
                         class="w-full h-full object-cover group-hover:scale-[1.06] transition-transform duration-500 ease-out {{ !$hasStock ? 'opacity-30 grayscale' : '' }}"
                         loading="lazy">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white/10">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif

                {{-- Badges --}}
                @if(!$hasStock)
                    <span class="absolute top-3 right-3 text-white/80 text-[10px] px-3 py-1 rounded-full font-bold tracking-wide" style="background:rgba(80,80,80,0.85);backdrop-filter:blur(4px);">نفذ</span>
                @elseif($product->sale_price)
                    @php $discount = round((($product->base_price - $product->sale_price) / $product->base_price) * 100); @endphp
                    <span class="absolute top-3 right-3 bg-brand-red text-white text-[10px] px-3 py-1 rounded-full font-bold">-{{ $discount }}%</span>
                @endif

                {{-- Hover overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>
            <div class="p-4 space-y-2">
                <h3 class="text-white/90 text-sm font-semibold line-clamp-1 group-hover:text-white transition-colors">{{ $product->name_ar ?? $product->name }}</h3>
                <div class="flex items-baseline gap-2">
                    @if(!$hasStock)
                        <span class="text-white/25 text-sm font-bold">غير متوفر</span>
                    @elseif($product->sale_price)
                        <span class="text-brand-red font-black text-[15px]">{{ number_format($product->sale_price) }} <span class="text-xs font-medium">ج.م</span></span>
                        <span class="text-white/30 text-xs line-through">{{ number_format($product->base_price) }}</span>
                    @else
                        <span class="text-white font-black text-[15px]">{{ number_format($product->base_price) }} <span class="text-xs font-medium text-white/50">ج.م</span></span>
                    @endif
                </div>
            </div>
        </div>
    </a>

    {{-- Wishlist button --}}
    @auth
    @php $inWish = auth()->user()->wishlistItems()->where('product_id', $product->id)->exists(); @endphp
    <button type="button" data-wishlist-id="{{ $product->id }}"
        data-in-wishlist="{{ $inWish ? '1' : '0' }}"
        class="absolute top-3 left-3 z-10 opacity-0 group-hover:opacity-100 transition-all duration-200"
        style="width:34px;height:34px;border-radius:50%;background:rgba(0,0,0,0.55);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;"
        title="{{ $inWish ? 'إزالة من المفضلة' : 'أضف للمفضلة' }}">
        <svg style="width:16px;height:16px;color:{{ $inWish ? '#e63946' : 'rgba(255,255,255,0.7)' }};" fill="{{ $inWish ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
    </button>
    @endauth
</div>
