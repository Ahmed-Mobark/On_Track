@extends('layouts.app')
@section('title', 'السلة')

@push('styles')
<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; appearance: textfield; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-white mb-8">سلة التسوق</h1>

    @if(count($items))
        <div class="space-y-4 mb-8">
            @foreach($items as $item)
                <div class="bg-brand-dark rounded-xl p-4 flex items-center gap-4">
                    {{-- Product image + link --}}
                    <a href="{{ route('product.show', $item['product']->slug) }}" class="w-20 h-20 bg-white/5 rounded-lg overflow-hidden flex-shrink-0 block">
                        @if($item['product']->images->first())
                            <img src="{{ $item['product']->images->first()->url }}" class="w-full h-full object-cover">
                        @endif
                    </a>
                    {{-- Product info + link --}}
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('product.show', $item['product']->slug) }}" style="text-decoration:none;">
                            <h3 class="text-white font-medium text-sm hover:text-brand-red transition-colors">{{ $item['product']->name_ar ?? $item['product']->name }}</h3>
                        </a>
                        <p class="text-white/40 text-xs mt-1">{{ $item['variant']->size }} / {{ $item['variant']->color }}</p>
                        <p style="color:#e63946;font-weight:700;margin-top:4px;">{{ number_format($item['price']) }} ج.م</p>
                    </div>
                    {{-- Quantity + Delete --}}
                    <div class="flex items-center gap-3">
                        <form action="{{ route('cart.update', $item['key']) }}" method="POST" id="cart-form-{{ $item['key'] }}">
                            @csrf @method('PATCH')
                            <div dir="ltr" style="display:flex;align-items:center;border:1px solid rgba(255,255,255,0.15);border-radius:8px;overflow:hidden;">
                                <button type="button" onclick="cartQty('{{ $item['key'] }}',-1)"
                                    style="width:34px;height:34px;background:rgba(255,255,255,0.05);border:none;color:white;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">−</button>
                                <span style="width:38px;height:34px;display:flex;align-items:center;justify-content:center;color:white;font-size:15px;font-weight:700;border-right:1px solid rgba(255,255,255,0.1);border-left:1px solid rgba(255,255,255,0.1);">{{ $item['quantity'] }}</span>
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] }}" id="qty-{{ $item['key'] }}">
                                <button type="button" onclick="cartQty('{{ $item['key'] }}',1)"
                                    style="width:34px;height:34px;background:rgba(255,255,255,0.05);border:none;color:white;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">+</button>
                            </div>
                        </form>
                        <form action="{{ route('cart.remove', $item['key']) }}" method="POST">
                            @csrf @method('DELETE')
                            <button style="width:34px;height:34px;border-radius:8px;background:rgba(239,68,68,0.1);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                                onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                                <svg style="width:14px;height:14px;color:#f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Summary --}}
        <div class="bg-brand-dark rounded-xl p-6">
            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-white/60">
                    <span>المجموع الفرعي</span>
                    <span>{{ number_format($subtotal) }} ج.م</span>
                </div>
                <div class="flex justify-between text-white/60">
                    <span>الشحن</span>
                    <span>يحسب عند الـ checkout</span>
                </div>
                <div class="flex justify-between text-white font-bold text-lg pt-3 border-t border-white/10">
                    <span>الإجمالي</span>
                    <span>{{ number_format($subtotal) }} ج.م</span>
                </div>
            </div>
            <a href="{{ route('checkout') }}"
               style="display:block;width:100%;background:#e63946;color:white;font-weight:700;padding:16px;border-radius:12px;text-align:center;font-size:16px;margin-top:20px;text-decoration:none;transition:background 0.3s;"
               onmouseover="this.style.background='#c1121f'" onmouseout="this.style.background='#e63946'">
                إتمام الشراء
            </a>
            <a href="{{ route('shop') }}" style="display:block;text-align:center;color:rgba(255,255,255,0.4);font-size:13px;margin-top:12px;text-decoration:none;">
                ← متابعة التسوق
            </a>
        </div>
    @else
        <div class="text-center py-16">
            <svg style="width:48px;height:48px;margin:0 auto 16px;color:rgba(255,255,255,0.1);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <p class="text-white/40 text-lg mb-4">السلة فارغة</p>
            <a href="{{ route('shop') }}" style="color:#e63946;font-size:14px;font-weight:600;">تصفح المتجر</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function cartQty(key, delta) {
    const inp = document.getElementById('qty-' + key);
    let v = parseInt(inp.value) + delta;
    if (v < 0) v = 0;
    if (v > 99) v = 99;
    inp.value = v;
    document.getElementById('cart-form-' + key).submit();
}
</script>
@endpush
