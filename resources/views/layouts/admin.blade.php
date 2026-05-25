<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - On Track Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-black': '#0a0a0a',
                        'brand-dark': '#141414',
                        'brand-red': '#e63946',
                        'brand-red-dark': '#c1121f',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
    @stack('styles')
</head>
<body class="bg-brand-black text-white min-h-screen flex">
    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed lg:static inset-y-0 right-0 z-50 w-64 bg-brand-dark border-l border-white/10 transform translate-x-full lg:translate-x-0 transition-transform">
        <div class="flex items-center justify-between h-16 px-6 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <img src="/images/brand/logo.png" alt="On Track" class="h-7 w-auto">
                <span class="text-white/40 text-xs font-medium">Admin</span>
            </a>
            <button onclick="toggleSidebar()" class="lg:hidden text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="p-4 space-y-1">
            @php
                $links = [
                    ['route' => 'admin.dashboard', 'label' => 'لوحة التحكم', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'admin.products.index', 'label' => 'المنتجات', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['route' => 'admin.categories.index', 'label' => 'التصنيفات', 'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z'],
                    ['route' => 'admin.orders.index', 'label' => 'الطلبات', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                    ['route' => 'admin.customers.index', 'label' => 'العملاء', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['route' => 'admin.coupons.index', 'label' => 'الكوبونات', 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
                    ['route' => 'admin.pos.index', 'label' => 'نقطة البيع', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['route' => 'admin.inventory.index', 'label' => 'المخزون', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                    ['route' => 'admin.shipping.index', 'label' => 'الشحن', 'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
                    ['route' => 'admin.banners.index', 'label' => 'العروض', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['route' => 'admin.analytics.index', 'label' => 'التحليلات', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['route' => 'admin.messages.index', 'label' => 'الرسائل', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['route' => 'admin.settings.index', 'label' => 'الإعدادات', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                ];
            @endphp

            @foreach($links as $link)
                @php $isActive = request()->routeIs($link['route'] . '*'); @endphp
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ $isActive ? 'bg-brand-red/10 text-brand-red' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-white/60 hover:text-white hover:bg-white/5 w-full transition-colors">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/50 hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col min-h-screen">
        <header class="h-16 border-b border-white/10 flex items-center px-4 lg:px-8 gap-4 relative overflow-hidden">
            <img src="/images/brand/bgline.png" alt="" class="absolute inset-0 w-full h-full object-cover opacity-[0.03] pointer-events-none select-none">
            <button onclick="toggleSidebar()" class="lg:hidden text-white relative z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex-1"></div>
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2 text-white/60 hover:text-white text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                عرض المتجر
            </a>
            <span class="text-white/40 text-sm">{{ auth()->user()->name ?? '' }}</span>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="px-4 lg:px-8 pt-4">
                <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="px-4 lg:px-8 pt-4">
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg text-sm">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            </div>
        @endif

        <main class="flex-1 p-4 lg:p-8">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
