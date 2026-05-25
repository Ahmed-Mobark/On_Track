@extends('layouts.app')
@section('title', 'الرئيسية')

@push('styles')
<style>
    /* Hero Animations — fast & crisp */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.94); }
        to { opacity: 1; transform: scale(1); }
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 15px rgba(230, 57, 70, 0.2); }
        50% { box-shadow: 0 0 30px rgba(230, 57, 70, 0.4); }
    }
    @keyframes line-expand {
        from { width: 0; }
        to { width: 100%; }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    /* Snappy spring easing — cubic-bezier(0.16, 1, 0.3, 1) */
    .hero-logo { animation: scaleIn 0.5s cubic-bezier(0.16,1,0.3,1) 0.05s both; }
    .hero-tagline { animation: fadeInUp 0.45s cubic-bezier(0.16,1,0.3,1) 0.15s both; }
    .hero-subtitle { animation: fadeInUp 0.45s cubic-bezier(0.16,1,0.3,1) 0.25s both; }
    .hero-cta { animation: fadeInUp 0.45s cubic-bezier(0.16,1,0.3,1) 0.35s both; }
    .hero-line { animation: line-expand 0.7s cubic-bezier(0.16,1,0.3,1) 0.2s both; }
    .hero-badge { animation: slideDown 0.35s cubic-bezier(0.16,1,0.3,1) 0.45s both; }
    .hero-stat { animation: fadeInUp 0.35s cubic-bezier(0.16,1,0.3,1) both; }

    .hero-grain::before {
        content: '';
        position: absolute;
        inset: -50%;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.02'/%3E%3C/svg%3E");
        pointer-events: none;
    }

    /* Sections — fast reveal on scroll */
    .section-reveal {
        opacity: 0;
        transform: translateY(16px);
        transition: opacity 0.4s cubic-bezier(0.16,1,0.3,1), transform 0.4s cubic-bezier(0.16,1,0.3,1);
    }
    .section-reveal.visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endpush

@section('content')
{{-- HERO SECTION --}}
<section class="relative min-h-[90vh] flex items-center overflow-hidden hero-grain">
    {{-- Background layers --}}
    <div class="absolute inset-0 bg-gradient-to-br from-brand-black via-brand-dark to-brand-black"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-brand-black via-transparent to-transparent"></div>

    {{-- Diagonal accent line --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-1/2 -right-1/4 w-[800px] h-[800px] bg-brand-red/5 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-1/4 -left-1/4 w-[600px] h-[600px] bg-brand-red/3 rounded-full blur-[100px]"></div>
    </div>

    {{-- Geometric lines --}}
    <div class="absolute inset-0 pointer-events-none opacity-10">
        <div class="absolute top-1/4 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent hero-line"></div>
        <div class="absolute top-3/4 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/10 to-transparent hero-line" style="animation-delay: 0.8s"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 w-full">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            {{-- Left: Content --}}
            <div class="text-center lg:text-right order-2 lg:order-1">
                {{-- Badge --}}
                <div class="hero-badge inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-brand-red/30 bg-brand-red/5 mb-6">
                    <span class="w-2 h-2 bg-brand-red rounded-full animate-pulse"></span>
                    <span class="text-brand-red text-xs font-semibold tracking-wider">PERFORMANCE IN MOTION</span>
                </div>

                {{-- Main heading --}}
                <h1 class="hero-tagline text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-4">
                    <span class="block">ملابس رياضية</span>
                    <span class="block mt-2">
                        مصممة
                        <span class="relative inline-block">
                            <span class="text-brand-red">للأبطال</span>
                            <span class="absolute -bottom-1 left-0 w-full h-0.5 bg-brand-red/50 hero-line"></span>
                        </span>
                    </span>
                </h1>

                <p class="hero-subtitle text-white/50 text-lg md:text-xl max-w-lg mx-auto lg:mx-0 lg:mr-0 mb-8 leading-relaxed">
                    خامات بريميوم. تصميم يدعم أداءك. جودة تدوم معاك في كل تمرين.
                </p>

                {{-- CTA Buttons --}}
                <div class="hero-cta flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('shop') }}"
                       class="group relative bg-brand-red hover:bg-brand-red-dark text-white font-bold px-10 py-4 rounded-xl text-lg transition-all duration-300 overflow-hidden"
                       style="animation: pulse-glow 3s ease-in-out infinite;">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            تسوق الآن
                            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </span>
                    </a>
                    <a href="#categories"
                       class="border border-white/20 hover:border-white/40 text-white font-semibold px-10 py-4 rounded-xl text-lg transition-all duration-300 hover:bg-white/5 text-center">
                        تصفح التصنيفات
                    </a>
                </div>

                {{-- Stats --}}
                <div class="hero-cta flex items-center gap-8 mt-10 justify-center lg:justify-start" style="animation-delay: 1.6s">
                    @foreach([
                        ['value' => '+500', 'label' => 'منتج'],
                        ['value' => '+2K', 'label' => 'عميل سعيد'],
                        ['value' => '100%', 'label' => 'جودة مضمونة'],
                    ] as $i => $stat)
                        <div class="hero-stat text-center" style="animation-delay: {{ 1.6 + ($i * 0.15) }}s">
                            <p class="text-white text-xl md:text-2xl font-black">{{ $stat['value'] }}</p>
                            <p class="text-white/30 text-xs mt-0.5">{{ $stat['label'] }}</p>
                        </div>
                        @if(!$loop->last)
                            <div class="w-px h-8 bg-white/10"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Right: Logo visual --}}
            <div class="order-1 lg:order-2 flex justify-center lg:justify-end">
                <div class="hero-logo relative">
                    {{-- Glow behind logo --}}
                    <div class="absolute inset-0 bg-brand-red/10 rounded-full blur-[80px] scale-150"></div>

                    {{-- Logo --}}
                    <div class="relative" style="animation: float 6s ease-in-out infinite;">
                        <img src="/images/brand/logo.png"
                             alt="On Track"
                             class="w-64 md:w-80 lg:w-96 h-auto drop-shadow-2xl">

                        {{-- Decorative ring --}}
                        <div class="absolute -inset-8 border border-white/5 rounded-full animate-spin" style="animation-duration: 30s;"></div>
                        <div class="absolute -inset-16 border border-white/[0.02] rounded-full animate-spin" style="animation-duration: 45s; animation-direction: reverse;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-bounce">
        <span class="text-white/20 text-xs">اكتشف المزيد</span>
        <svg class="w-5 h-5 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
    </div>
</section>

{{-- OFFERS SLIDER --}}
@if($banners->count())
<section class="py-10 section-reveal">
    <div class="max-w-7xl mx-auto px-4">
        <div class="relative overflow-hidden rounded-2xl" id="offers-slider">
            <div class="flex transition-transform duration-500 ease-out" id="offers-track" style="width:{{ $banners->count() * 100 }}%">
                @foreach($banners as $banner)
                    <div style="width:{{ 100 / $banners->count() }}%">
                        <a href="{{ $banner->link ?? '#' }}" class="block relative aspect-[2.4/1] overflow-hidden rounded-2xl">
                            <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                            @if($banner->title || $banner->button_text)
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent flex items-end">
                                    <div class="p-6 md:p-10">
                                        @if($banner->title)
                                            <h3 class="text-white text-xl md:text-3xl font-bold mb-2">{{ $banner->title }}</h3>
                                        @endif
                                        @if($banner->subtitle)
                                            <p class="text-white/60 text-sm md:text-base mb-4">{{ $banner->subtitle }}</p>
                                        @endif
                                        @if($banner->button_text)
                                            <span class="inline-block bg-brand-red text-white px-6 py-2 rounded-lg text-sm font-bold">{{ $banner->button_text }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
            @if($banners->count() > 1)
                <button onclick="slideOffers(-1)" class="absolute top-1/2 right-3 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-colors z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button onclick="slideOffers(1)" class="absolute top-1/2 left-3 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-colors z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                    @foreach($banners as $i => $b)
                        <button onclick="goToSlide({{ $i }})" class="offer-dot w-2 h-2 rounded-full {{ $i === 0 ? 'bg-white' : 'bg-white/40' }} transition-colors"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- SECTIONS / TAB BAR --}}
@if($categories->count())
<section class="py-20 section-reveal" id="categories">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-10">
            <span class="text-brand-red text-xs font-bold tracking-widest uppercase">CATEGORIES</span>
            <h2 class="text-3xl font-black text-white mt-1">الأقسام</h2>
        </div>

        {{-- Tab Bar --}}
        <div id="category-tabs" style="display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:40px;overflow-x:auto;padding:6px;-webkit-overflow-scrolling:touch;background:rgba(255,255,255,0.03);border-radius:16px;border:1px solid rgba(255,255,255,0.06);">
            {{-- "الكل" tab --}}
            <button onclick="switchTab('all')" id="tab-all"
                style="padding:10px 28px;border-radius:12px;font-size:14px;font-weight:700;white-space:nowrap;transition:all 0.3s;cursor:pointer;border:none;color:#e63946;background:rgba(230,57,70,0.15);">
                الكل
            </button>
            @foreach($categories as $category)
                <button onclick="switchTab('{{ $category->id }}')"
                    id="tab-{{ $category->id }}"
                    style="padding:10px 24px;border-radius:12px;font-size:14px;font-weight:600;white-space:nowrap;transition:all 0.3s;cursor:pointer;border:none;color:rgba(255,255,255,0.5);background:transparent;">
                    {{ $category->name_ar ?? $category->name }}
                </button>
            @endforeach
        </div>

        {{-- "الكل" content --}}
        <div id="tab-content-all" class="cat-tab-content">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @php
                    $allProducts = collect();
                    foreach($categoryProducts as $prods) { $allProducts = $allProducts->merge($prods); }
                    $allProducts = $allProducts->unique('id')->take(8);
                @endphp
                @foreach($allProducts as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('shop') }}"
                   style="display:inline-flex;align-items:center;gap:6px;color:#e63946;font-size:14px;font-weight:600;padding:12px 32px;border:1px solid rgba(230,57,70,0.3);border-radius:12px;transition:all 0.3s;"
                   onmouseover="this.style.background='rgba(230,57,70,0.1)';this.style.borderColor='#e63946'" onmouseout="this.style.background='transparent';this.style.borderColor='rgba(230,57,70,0.3)'">
                    عرض كل المنتجات
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            </div>
        </div>

        {{-- Category contents --}}
        @foreach($categories as $category)
            <div id="tab-content-{{ $category->id }}" class="cat-tab-content" style="display:none;">
                @if(isset($categoryProducts[$category->id]) && $categoryProducts[$category->id]->count())
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                        @foreach($categoryProducts[$category->id] as $product)
                            @include('components.product-card', ['product' => $product])
                        @endforeach
                    </div>
                    <div class="text-center mt-8">
                        <a href="{{ route('shop', ['category' => $category->slug]) }}"
                           style="display:inline-flex;align-items:center;gap:6px;color:#e63946;font-size:14px;font-weight:600;padding:12px 32px;border:1px solid rgba(230,57,70,0.3);border-radius:12px;transition:all 0.3s;"
                           onmouseover="this.style.background='rgba(230,57,70,0.1)';this.style.borderColor='#e63946'" onmouseout="this.style.background='transparent';this.style.borderColor='rgba(230,57,70,0.3)'">
                            عرض كل {{ $category->name_ar ?? $category->name }}
                            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    </div>
                @else
                    <div style="text-align:center;padding:60px 0;">
                        <svg style="width:48px;height:48px;margin:0 auto 16px;color:rgba(255,255,255,0.1);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p style="color:rgba(255,255,255,0.3);font-size:16px;">لا توجد منتجات في هذا القسم حالياً</p>
                        <a href="{{ route('shop') }}" style="color:#e63946;font-size:13px;margin-top:8px;display:inline-block;">تصفح كل المنتجات</a>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- BEST SELLERS --}}
@if($bestSellers->count())
<section class="py-20 section-reveal">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-brand-red text-xs font-bold tracking-widest uppercase">TOP SELLERS</span>
                <h2 class="text-3xl font-black text-white mt-1">الأكثر <span class="text-brand-red">مبيعاً</span></h2>
            </div>
            <a href="{{ route('shop', ['sort' => 'popular']) }}" class="text-brand-red text-sm hover:underline font-medium flex items-center gap-1">
                عرض الكل
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            @foreach($bestSellers as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- FEATURED BANNER --}}
<section class="py-16 section-reveal">
    <div class="max-w-7xl mx-auto px-4">
        <div class="relative bg-gradient-to-l from-brand-red/20 via-brand-dark to-brand-dark rounded-3xl overflow-hidden p-8 md:p-14">
            <img src="/images/brand/bgline.png" alt="" class="absolute inset-0 w-full h-full object-cover opacity-[0.04] pointer-events-none select-none">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-brand-red/10 to-transparent"></div>
            <div class="relative z-10 max-w-lg">
                <span class="text-brand-red text-xs font-bold tracking-widest uppercase">عرض خاص</span>
                <h3 class="text-3xl md:text-4xl font-black text-white mt-2 mb-4">خصم يصل إلى<br><span class="text-brand-red">30%</span> على المجموعة الجديدة</h3>
                <p class="text-white/50 mb-6">لفترة محدودة. استغل العرض قبل ما يخلص!</p>
                <a href="{{ route('shop') }}" class="inline-block bg-brand-red hover:bg-brand-red-dark text-white font-bold px-8 py-3 rounded-xl transition-colors">
                    تسوق العروض
                </a>
            </div>
            {{-- Decorative --}}
            <div class="absolute top-1/2 right-10 -translate-y-1/2 hidden lg:block">
                <img src="/images/brand/05_ontrack_icon_mark_white.svg" alt="" class="w-48 h-48 opacity-5">
            </div>
        </div>
    </div>
</section>

{{-- NEW ARRIVALS --}}
@if($newArrivals->count())
<section class="py-20 section-reveal">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-brand-red text-xs font-bold tracking-widest uppercase">JUST DROPPED</span>
                <h2 class="text-3xl font-black text-white mt-1">وصل <span class="text-brand-red">حديثاً</span></h2>
            </div>
            <a href="{{ route('shop', ['sort' => 'newest']) }}" class="text-brand-red text-sm hover:underline font-medium flex items-center gap-1">
                عرض الكل
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            @foreach($newArrivals as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- FEATURES --}}
<section class="py-20 border-t border-white/5 section-reveal">
    <div class="mx-auto max-w-7xl px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach([
                ['icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'title' => 'شحن مجاني', 'desc' => 'للطلبات فوق 500 ج.م'],
                ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'title' => 'إرجاع سهل', 'desc' => 'خلال 14 يوم'],
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'دفع آمن', 'desc' => '100% حماية'],
                ['icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'title' => 'جودة بريميوم', 'desc' => 'مصممة للأداء'],
            ] as $feature)
                <div class="text-center p-6 rounded-2xl bg-brand-dark/50 border border-white/5 hover:border-white/10 transition-colors">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-brand-red/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $feature['icon'] }}"/></svg>
                    </div>
                    <h3 class="text-white font-bold mb-1">{{ $feature['title'] }}</h3>
                    <p class="text-white/40 text-sm">{{ $feature['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Scroll reveal animation
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.section-reveal').forEach(el => observer.observe(el));

    // Offers slider
    let currentSlide = 0;
    const totalSlides = {{ $banners->count() ?? 0 }};
    const track = document.getElementById('offers-track');
    const dots = document.querySelectorAll('.offer-dot');

    function goToSlide(i) {
        currentSlide = i;
        if (track) track.style.transform = `translateX(${i * (100 / totalSlides)}%)`;
        dots.forEach((d, idx) => d.className = 'offer-dot w-2 h-2 rounded-full transition-colors ' + (idx === i ? 'bg-white' : 'bg-white/40'));
    }
    function slideOffers(dir) {
        goToSlide((currentSlide - dir + totalSlides) % totalSlides);
    }
    if (totalSlides > 1) setInterval(() => slideOffers(-1), 5000);

    // Category tabs
    function switchTab(categoryId) {
        // Hide all
        document.querySelectorAll('.cat-tab-content').forEach(el => {
            el.style.display = 'none';
        });
        // Show target with animation
        const target = document.getElementById('tab-content-' + categoryId);
        if (target) {
            target.style.display = 'block';
            target.style.opacity = '0';
            target.style.transform = 'translateY(16px)';
            requestAnimationFrame(() => {
                target.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                target.style.opacity = '1';
                target.style.transform = 'translateY(0)';
            });
        }
        // Update tab buttons
        document.querySelectorAll('#category-tabs button').forEach(btn => {
            const isActive = btn.id === 'tab-' + categoryId;
            btn.style.color = isActive ? '#e63946' : 'rgba(255,255,255,0.5)';
            btn.style.background = isActive ? 'rgba(230,57,70,0.15)' : 'transparent';
            btn.style.fontWeight = isActive ? '700' : '600';
        });
    }
</script>
@endpush
