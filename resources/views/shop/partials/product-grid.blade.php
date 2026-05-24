@if($products->count())
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        @foreach($products as $product)
            @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
    <div class="mt-8">
        {{ $products->links() }}
    </div>
@else
    <div style="text-align:center;padding:80px 0;">
        <svg style="width:48px;height:48px;margin:0 auto 16px;color:rgba(255,255,255,0.1);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <p style="color:rgba(255,255,255,0.3);font-size:18px;margin-bottom:8px;">لا توجد منتجات</p>
        <p style="color:rgba(255,255,255,0.2);font-size:14px;margin-bottom:20px;">جرب تغيير الفلتر أو البحث</p>
        <a href="{{ route('shop') }}" style="color:#e63946;font-size:14px;font-weight:600;">عرض كل المنتجات</a>
    </div>
@endif
