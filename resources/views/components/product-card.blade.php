@php
    $hasStock = $product->variants->where('quantity', '>', 0)->count() > 0;
@endphp
<div class="relative group">
    <a href="{{ route('product.show', $product->slug) }}">
        <div class="bg-brand-dark rounded-xl overflow-hidden border border-white/5 hover:border-white/10 transition-colors">
            <div class="aspect-square bg-white/5 relative overflow-hidden">
                @if($product->images->first())
                    <img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 {{ !$hasStock ? 'opacity-40 grayscale' : '' }}">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white/20">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
                @if(!$hasStock)
                    <span class="absolute top-2 right-2 text-white text-xs px-2 py-1 rounded-lg font-medium" style="background:rgba(100,100,100,0.8);">غير متوفر</span>
                @elseif($product->sale_price)
                    <span class="absolute top-2 right-2 bg-brand-red text-white text-xs px-2 py-1 rounded-lg font-medium">خصم</span>
                @endif
            </div>
            <div class="p-4">
                <h3 class="text-white text-sm font-medium mb-2 line-clamp-1">{{ $product->name_ar ?? $product->name }}</h3>
                <div class="flex items-center gap-2">
                    @if(!$hasStock)
                        <span class="text-white/30 font-bold">غير متوفر</span>
                    @elseif($product->sale_price)
                        <span class="text-brand-red font-bold">{{ number_format($product->sale_price) }} ج.م</span>
                        <span class="text-white/40 text-sm line-through">{{ number_format($product->base_price) }}</span>
                    @else
                        <span class="text-white font-bold">{{ number_format($product->base_price) }} ج.م</span>
                    @endif
                </div>
            </div>
        </div>
    </a>

    {{-- Wishlist button on card --}}
    @auth
    @php $inWish = auth()->user()->wishlistItems()->where('product_id', $product->id)->exists(); @endphp
    <button type="button" data-wishlist-id="{{ $product->id }}"
        data-in-wishlist="{{ $inWish ? '1' : '0' }}"
        class="absolute top-2 left-2 z-10"
        style="width:32px;height:32px;border-radius:50%;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.3s;"
        title="{{ $inWish ? 'إزالة من المفضلة' : 'أضف للمفضلة' }}">
        <svg style="width:16px;height:16px;color:{{ $inWish ? '#e63946' : 'rgba(255,255,255,0.6)' }};" fill="{{ $inWish ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
    </button>
    @endauth
</div>
