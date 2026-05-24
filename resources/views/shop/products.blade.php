@extends('layouts.app')
@section('title', 'المتجر')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-white mb-6">المتجر</h1>

    {{-- Category Tabs --}}
    @if($categories->count())
    <div style="display:flex;align-items:center;gap:6px;margin-bottom:24px;overflow-x:auto;padding:6px;-webkit-overflow-scrolling:touch;background:rgba(255,255,255,0.03);border-radius:14px;border:1px solid rgba(255,255,255,0.06);">
        <a href="{{ route('shop', request()->except('category')) }}"
           style="padding:9px 22px;border-radius:10px;font-size:13px;font-weight:{{ !request('category') ? '700' : '600' }};white-space:nowrap;transition:all 0.3s;text-decoration:none;
                  color:{{ !request('category') ? '#e63946' : 'rgba(255,255,255,0.5)' }};
                  background:{{ !request('category') ? 'rgba(230,57,70,0.15)' : 'transparent' }};">
            الكل
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('shop', array_merge(request()->except('category', 'page'), ['category' => $cat->slug])) }}"
               style="padding:9px 22px;border-radius:10px;font-size:13px;font-weight:{{ request('category') === $cat->slug ? '700' : '600' }};white-space:nowrap;transition:all 0.3s;text-decoration:none;
                      color:{{ request('category') === $cat->slug ? '#e63946' : 'rgba(255,255,255,0.5)' }};
                      background:{{ request('category') === $cat->slug ? 'rgba(230,57,70,0.15)' : 'transparent' }};">
                {{ $cat->name_ar ?? $cat->name }}
            </a>
        @endforeach
    </div>
    @endif

    {{-- Search & Sort --}}
    <form id="shop-filter-form" method="GET" action="{{ route('shop') }}" class="flex flex-wrap items-center gap-3 mb-8">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <div class="flex-1 min-w-[200px] relative">
            <input type="text" name="search" id="live-search-input" value="{{ request('search') }}" placeholder="بحث بالاسم..."
                autocomplete="off"
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red">
            <div id="search-spinner" style="display:none;position:absolute;left:12px;top:50%;transform:translateY(-50%);">
                <svg style="width:16px;height:16px;animation:spin 1s linear infinite;color:rgba(255,255,255,0.4);" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.3"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
            </div>
        </div>
        <select name="sort" id="sort-select" onchange="this.form.submit()"
            style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px 16px;color:white;font-size:13px;">
            <option value="" {{ !request('sort') ? 'selected' : '' }}>الأحدث</option>
            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>السعر: الأقل</option>
            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>السعر: الأعلى</option>
            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>الأكثر مبيعاً</option>
        </select>
    </form>

    {{-- Active filter indicator --}}
    @if(request('category') || request('search'))
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
        @if(request('category'))
            @php $activeCat = $categories->firstWhere('slug', request('category')); @endphp
            @if($activeCat)
            <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-size:12px;background:rgba(230,57,70,0.1);color:#e63946;border:1px solid rgba(230,57,70,0.2);">
                {{ $activeCat->name_ar ?? $activeCat->name }}
                <a href="{{ route('shop', request()->except('category')) }}" style="color:#e63946;font-size:14px;text-decoration:none;">&times;</a>
            </span>
            @endif
        @endif
        @if(request('search'))
            <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-size:12px;background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.6);border:1px solid rgba(255,255,255,0.1);">
                "{{ request('search') }}"
                <a href="{{ route('shop', request()->except('search')) }}" style="color:rgba(255,255,255,0.4);font-size:14px;text-decoration:none;">&times;</a>
            </span>
        @endif
        <span style="color:rgba(255,255,255,0.3);font-size:12px;">{{ $products->total() }} منتج</span>
    </div>
    @endif

    {{-- Products Grid --}}
    <div id="products-container">
        @include('shop.partials.product-grid', ['products' => $products])
    </div>
</div>

@push('scripts')
<style>@keyframes spin{from{transform:translateY(-50%) rotate(0deg)}to{transform:translateY(-50%) rotate(360deg)}}</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('live-search-input');
    const container = document.getElementById('products-container');
    const spinner = document.getElementById('search-spinner');
    const form = document.getElementById('shop-filter-form');
    let debounceTimer;
    let abortController;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => performSearch(), 300);
    });

    document.getElementById('sort-select').addEventListener('change', function() {
        performSearch();
    });

    function performSearch() {
        if (abortController) abortController.abort();
        abortController = new AbortController();

        const params = new URLSearchParams(new FormData(form));
        spinner.style.display = 'block';

        fetch('{{ route("shop") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: abortController.signal
        })
        .then(res => res.json())
        .then(data => {
            container.innerHTML = data.html;
            spinner.style.display = 'none';
            // Update URL without reload
            const url = new URL(window.location);
            url.search = params.toString();
            history.replaceState(null, '', url);
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                spinner.style.display = 'none';
            }
        });
    }

    // Prevent form submit (fallback for Enter key)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        performSearch();
    });
});
</script>
@endpush
@endsection
