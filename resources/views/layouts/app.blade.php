<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'On Track') - ملابس رياضية</title>
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
    <style>
        body { font-family: 'Cairo', sans-serif; }

        /* Global smooth transitions */
        *, *::before, *::after { -webkit-tap-highlight-color: transparent; }
        a, button { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        img { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease; }

        /* Smooth page load */
        body { animation: pageIn 0.35s ease-out; }
        @keyframes pageIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        /* Snappy hover on cards */
        .product-hover {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }
        .product-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 48px rgba(0,0,0,0.35);
        }
        .product-hover:active { transform: translateY(-2px) scale(0.98); }

        /* Smooth scroll */
        html { scroll-behavior: smooth; }

        /* Button press effect */
        .btn-press:active { transform: scale(0.96); }

        /* Skeleton loading shimmer */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, rgba(255,255,255,0.03) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.03) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        /* Scroll indicator fade */
        @keyframes scrollPulse {
            0%, 100% { opacity: 0.4; transform: translateY(0); }
            50% { opacity: 0.8; transform: translateY(4px); }
        }

        /* Glass card effect */
        .glass-card {
            background: rgba(20, 20, 20, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.06);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        /* Toast animation */
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(-50%) translateY(12px); }
            to { opacity: 1; transform: translateX(-50%) translateY(0); }
        }

        /* Radio/checkbox custom styling */
        input[type="radio"] {
            accent-color: #e63946;
        }

        /* Focus ring */
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #e63946 !important;
            box-shadow: 0 0 0 2px rgba(230, 57, 70, 0.15);
        }

        /* Selection color */
        ::selection { background: rgba(230, 57, 70, 0.3); color: white; }
    </style>
    @stack('styles')
</head>
<body class="bg-brand-black text-white min-h-screen">
    {{-- Header --}}
    <header class="border-b border-white/5 sticky top-0 z-50 bg-brand-black/90 backdrop-blur-md relative overflow-hidden">
        <img src="/images/brand/bgline.png" alt="" class="absolute inset-0 w-full h-full object-cover opacity-[0.02] pointer-events-none select-none">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between relative z-10">
            <a href="{{ route('home') }}" class="shrink-0">
                <img src="/images/brand/logo.png" alt="On Track" class="h-8 w-auto hover:opacity-80 transition-opacity">
            </a>

            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-white/60 hover:text-white text-sm font-medium relative group">
                    الرئيسية
                    @if(request()->routeIs('home'))
                    <span class="absolute -bottom-1 right-0 left-0 h-0.5 bg-brand-red rounded-full"></span>
                    @endif
                </a>
                <a href="{{ route('shop') }}" class="text-white/60 hover:text-white text-sm font-medium relative">
                    المتجر
                    @if(request()->routeIs('shop'))
                    <span class="absolute -bottom-1 right-0 left-0 h-0.5 bg-brand-red rounded-full"></span>
                    @endif
                </a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('cart') }}" class="text-white/60 hover:text-white relative p-2 rounded-lg hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @if(session('cart') && count(session('cart')) > 0)
                        <span class="absolute top-0.5 right-0.5 bg-brand-red text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center font-bold leading-none">{{ count(session('cart')) }}</span>
                    @endif
                </a>
                @auth
                    <a href="{{ route('notifications.index') }}" class="text-white/60 hover:text-white relative p-2 rounded-lg hover:bg-white/5 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if(auth()->user()->unreadNotificationsCount() > 0)
                            <span id="notification-badge" class="absolute top-0.5 right-0.5 bg-brand-red text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center font-bold leading-none">{{ auth()->user()->unreadNotificationsCount() }}</span>
                        @else
                            <span id="notification-badge" class="absolute top-0.5 right-0.5 bg-brand-red text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center font-bold leading-none" style="display:none"></span>
                        @endif
                    </a>
                    <a href="{{ route('wishlist') }}" class="text-white/60 hover:text-white p-2 rounded-lg hover:bg-white/5 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </a>
                    <a href="{{ route('account') }}" class="text-white/60 hover:text-white p-2 rounded-lg hover:bg-white/5 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-white/60 hover:text-white font-medium px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">تسجيل الدخول</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4" id="flash-success" style="transition:opacity 0.4s;">
            <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        </div>
        <script>setTimeout(function(){var el=document.getElementById('flash-success');if(el){el.style.opacity='0';setTimeout(function(){el.remove();},400);}},3000);</script>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 mt-4" id="flash-errors" style="transition:opacity 0.4s;">
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
        <script>setTimeout(function(){var el=document.getElementById('flash-errors');if(el){el.style.opacity='0';setTimeout(function(){el.remove();},400);}},5000);</script>
    @endif

    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-white/5 py-16 mt-20 bg-gradient-to-b from-transparent to-brand-dark/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-10 mb-10">
                <div>
                    <h3 class="text-white font-semibold mb-3">On Track</h3>
                    <p class="text-white/40 text-sm mb-4">ملابس رياضية بريميوم مصممة للأداء العالي</p>
                    {{-- Social Media Icons --}}
                    <div class="flex items-center gap-3">
                        @if(!empty($siteSettings['facebook_url']))
                        <a href="{{ $siteSettings['facebook_url'] }}" target="_blank" class="text-white/40 hover:text-blue-400 transition-colors" title="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        @endif
                        @if(!empty($siteSettings['tiktok_url']))
                        <a href="{{ $siteSettings['tiktok_url'] }}" target="_blank" class="text-white/40 hover:text-white transition-colors" title="TikTok">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                        @endif
                        @if(!empty($siteSettings['instagram_url']))
                        <a href="{{ $siteSettings['instagram_url'] }}" target="_blank" class="text-white/40 hover:text-pink-400 transition-colors" title="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        @endif
                        @if(!empty($siteSettings['whatsapp']))
                        <a href="https://wa.me/{{ $siteSettings['whatsapp'] }}" target="_blank" class="text-white/40 hover:text-green-400 transition-colors" title="WhatsApp">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-3">روابط</h3>
                    <ul class="space-y-2 text-sm text-white/40">
                        <li><a href="{{ route('shop') }}" class="hover:text-white">المتجر</a></li>
                        <li><a href="#" class="hover:text-white">من نحن</a></li>
                        <li><a href="#" onclick="event.preventDefault();document.getElementById('contact-modal').classList.remove('hidden');" class="hover:text-white">تواصل معنا</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-3">خدمة العملاء</h3>
                    <ul class="space-y-2 text-sm text-white/40">
                        <li>شحن مجاني للطلبات فوق 2,000 ج.م</li>
                        <li>منتجات أصلية 100%</li>
                        <li>دفع آمن 100%</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-3">تواصل معنا</h3>
                    @if(!empty($siteSettings['email']))
                        <p class="text-white/40 text-sm mb-2">{{ $siteSettings['email'] }}</p>
                    @endif
                    @if(!empty($siteSettings['phone']))
                        <p class="text-white/40 text-sm mb-2" dir="ltr">{{ $siteSettings['phone'] }}</p>
                    @endif
                    @if(!empty($siteSettings['whatsapp']))
                    <a href="https://wa.me/{{ $siteSettings['whatsapp'] }}" target="_blank" class="inline-flex items-center gap-2 text-green-400 hover:text-green-300 text-sm mb-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        واتساب
                    </a>
                    @endif
                    @if(!empty($siteSettings['address']))
                        <p class="text-white/40 text-sm">{{ $siteSettings['address'] }}</p>
                    @endif
                </div>
            </div>
            <div class="border-t border-white/10 pt-6 text-center text-white/30 text-sm">
                &copy; {{ date('Y') }} On Track. جميع الحقوق محفوظة.
            </div>
        </div>
    </footer>

    {{-- Contact Us Modal --}}
    <div id="contact-modal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('contact-modal').classList.add('hidden')"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-brand-dark rounded-2xl border border-white/10 w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">
                <button onclick="document.getElementById('contact-modal').classList.add('hidden')" class="absolute top-4 left-4 text-white/40 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <h2 class="text-xl font-bold text-white mb-1">تواصل معنا</h2>
                <p class="text-white/40 text-sm mb-6">أرسلنا رسالتك وهنرد عليك في أقرب وقت</p>

                {{-- Contact Info --}}
                <div class="flex flex-wrap gap-3 mb-6">
                    @if(!empty($siteSettings['email']))
                    <a href="mailto:{{ $siteSettings['email'] }}" class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-white/60 hover:text-white hover:border-white/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $siteSettings['email'] }}
                    </a>
                    @endif
                    @if(!empty($siteSettings['whatsapp']))
                    <a href="https://wa.me/{{ $siteSettings['whatsapp'] }}" target="_blank" class="inline-flex items-center gap-2 bg-green-500/10 border border-green-500/20 rounded-lg px-3 py-2 text-sm text-green-400 hover:text-green-300 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        واتساب
                    </a>
                    @endif
                    @if(!empty($siteSettings['phone']))
                    <a href="tel:{{ $siteSettings['phone'] }}" class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-white/60 hover:text-white hover:border-white/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $siteSettings['phone'] }}
                    </a>
                    @endif
                </div>

                {{-- Contact Form --}}
                <form id="contact-form" class="space-y-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-white/70 text-sm mb-1">الاسم *</label>
                            <input type="text" name="name" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand-red placeholder-white/20" placeholder="اسمك">
                        </div>
                        <div>
                            <label class="block text-white/70 text-sm mb-1">رقم الموبايل</label>
                            <input type="text" name="phone" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand-red placeholder-white/20" dir="ltr" placeholder="01xxxxxxxxx">
                        </div>
                    </div>
                    <div>
                        <label class="block text-white/70 text-sm mb-1">البريد الإلكتروني</label>
                        <input type="email" name="email" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand-red placeholder-white/20" dir="ltr" placeholder="email@example.com">
                    </div>
                    <div>
                        <label class="block text-white/70 text-sm mb-1">الموضوع *</label>
                        <input type="text" name="subject" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand-red placeholder-white/20" placeholder="موضوع الرسالة">
                    </div>
                    <div>
                        <label class="block text-white/70 text-sm mb-1">الرسالة *</label>
                        <textarea name="message" required rows="4" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand-red placeholder-white/20 resize-none" placeholder="اكتب رسالتك هنا..."></textarea>
                    </div>
                    <button type="submit" id="contact-submit" class="w-full bg-brand-red hover:bg-brand-red-dark text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                        إرسال الرسالة
                    </button>
                </form>

                {{-- Success message (hidden by default) --}}
                <div id="contact-success" class="hidden text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-white font-bold text-lg mb-1">تم الإرسال بنجاح!</h3>
                    <p class="text-white/40 text-sm">هنتواصل معاك في أقرب وقت</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating buttons stack --}}
    <div class="fixed bottom-6 left-6 z-50 flex flex-col items-center gap-3">
        {{-- Cart floating icon --}}
        @php $cartCount = session('cart') ? count(session('cart')) : 0; @endphp
        <a href="{{ route('cart') }}" id="cart-fab"
           class="bg-brand-red hover:bg-brand-red-dark text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 relative"
           style="{{ $cartCount > 0 ? '' : 'opacity:0;transform:scale(0);pointer-events:none;' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span class="absolute -top-1 -right-1 bg-white text-brand-red text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-bold" id="cart-fab-count">
                {{ $cartCount }}
            </span>
        </a>

        {{-- WhatsApp Button --}}
        @if(!empty($siteSettings['whatsapp']))
        <a href="https://wa.me/{{ $siteSettings['whatsapp'] }}" target="_blank"
           class="bg-green-500 hover:bg-green-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg transition-colors">
            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>
        @endif
    </div>

    @if(session('success') && str_contains(session('success'), 'سلة'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fab = document.getElementById('cart-fab');
            if (fab) {
                setTimeout(() => {
                    fab.style.opacity = '1';
                    fab.style.transform = 'scale(1)';
                    fab.style.pointerEvents = 'auto';
                }, 200);
            }
        });
    </script>
    @endif

    <script>
    function showToast(message) {
        var existing = document.getElementById('app-toast');
        if (existing) existing.remove();
        var toast = document.createElement('div');
        toast.id = 'app-toast';
        toast.textContent = message;
        toast.style.cssText = 'position:fixed;bottom:100px;left:50%;transform:translateX(-50%) translateY(12px);background:rgba(20,20,20,0.97);color:white;padding:14px 28px;border-radius:14px;font-size:13px;z-index:9999;border:1px solid rgba(255,255,255,0.08);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);opacity:0;transition:all 0.35s cubic-bezier(0.34,1.56,0.64,1);box-shadow:0 8px 32px rgba(0,0,0,0.4);font-family:Cairo,sans-serif;';
        document.body.appendChild(toast);
        requestAnimationFrame(function() {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(-50%) translateY(0)';
        });
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(12px)';
        }, 2500);
        setTimeout(function() { toast.remove(); }, 3000);
    }

    // Wishlist toggle via event delegation - works everywhere
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-wishlist-id]');
        if (!btn || btn.disabled) return;
        e.preventDefault();
        e.stopPropagation();

        var productId = btn.getAttribute('data-wishlist-id');
        btn.disabled = true;
        btn.style.pointerEvents = 'none';

        var formData = new FormData();
        formData.append('product_id', productId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('/wishlist/toggle', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(function(r) {
            if (r.status === 401 || r.status === 419) {
                window.location.href = '/login';
                return null;
            }
            if (!r.ok) throw new Error('Status ' + r.status);
            return r.json();
        })
        .then(function(data) {
            if (!data) return;
            var added = data.added;
            btn.setAttribute('data-in-wishlist', added ? '1' : '0');

            // Update icon
            var svg = btn.querySelector('svg');
            if (svg) {
                svg.setAttribute('fill', added ? 'currentColor' : 'none');
                svg.style.color = added ? '#e63946' : 'rgba(255,255,255,0.7)';
            }
            btn.title = added ? 'إزالة من المفضلة' : 'أضف للمفضلة';

            // Update label if exists (product detail page)
            var label = btn.querySelector('span');
            if (label) label.textContent = added ? 'في المفضلة' : 'أضف للمفضلة';

            // Sync all buttons for same product
            document.querySelectorAll('[data-wishlist-id="' + productId + '"]').forEach(function(otherBtn) {
                if (otherBtn === btn) return;
                otherBtn.setAttribute('data-in-wishlist', added ? '1' : '0');
                var otherSvg = otherBtn.querySelector('svg');
                if (otherSvg) {
                    otherSvg.setAttribute('fill', added ? 'currentColor' : 'none');
                    otherSvg.style.color = added ? '#e63946' : 'rgba(255,255,255,0.7)';
                }
            });

            showToast(added ? 'تمت الإضافة للمفضلة' : 'تمت الإزالة من المفضلة');
        })
        .catch(function(e) {
            console.error('Wishlist error:', e);
            showToast('حدث خطأ، حاول مرة أخرى');
        })
        .finally(function() {
            btn.disabled = false;
            btn.style.pointerEvents = '';
        });
    });
    </script>

    <script>
    document.getElementById('contact-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        var btn = document.getElementById('contact-submit');
        btn.disabled = true;
        btn.textContent = 'جاري الإرسال...';

        var formData = new FormData(form);
        fetch('{{ route("contact.store") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(function(r) {
            if (!r.ok) return r.json().then(function(d) { throw d; });
            return r.json();
        })
        .then(function() {
            form.classList.add('hidden');
            document.getElementById('contact-success').classList.remove('hidden');
            form.reset();
            setTimeout(function() {
                document.getElementById('contact-modal').classList.add('hidden');
                form.classList.remove('hidden');
                document.getElementById('contact-success').classList.add('hidden');
            }, 3000);
        })
        .catch(function(err) {
            var msg = 'حدث خطأ، حاول مرة أخرى';
            if (err.errors) {
                var firstKey = Object.keys(err.errors)[0];
                msg = err.errors[firstKey][0];
            }
            showToast(msg);
        })
        .finally(function() {
            btn.disabled = false;
            btn.textContent = 'إرسال الرسالة';
        });
    });
    </script>

    {{-- Notification Sound + System Notifications + Polling --}}
    @auth
    <script>
        // Notification sound - pleasant two-tone chime
        window._notifSound = function() {
            try {
                var ctx = new (window.AudioContext || window.webkitAudioContext)();
                [0, 0.15].forEach(function(delay, i) {
                    var osc = ctx.createOscillator();
                    var gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.frequency.value = i === 0 ? 880 : 1100;
                    osc.type = 'sine';
                    gain.gain.setValueAtTime(0.3, ctx.currentTime + delay);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + delay + 0.3);
                    osc.start(ctx.currentTime + delay);
                    osc.stop(ctx.currentTime + delay + 0.3);
                });
            } catch(e) {}
        };

        // Show system notification (works even when tab is not focused)
        window._showSystemNotification = function(title, body, url) {
            window._notifSound();
            showToast(title);

            // Show native browser notification
            if ('Notification' in window && Notification.permission === 'granted') {
                var notif = new Notification(title, {
                    body: body || '',
                    icon: '/images/brand/logo.png',
                    badge: '/images/brand/logo.png',
                    dir: 'rtl',
                    silent: false,
                    requireInteraction: false,
                    tag: 'ontrack-' + Date.now(),
                });
                notif.onclick = function() {
                    window.focus();
                    if (url) window.location.href = url;
                    notif.close();
                };
            }

            // Update badge
            var badge = document.getElementById('notification-badge');
            if (badge) {
                var current = parseInt(badge.textContent) || 0;
                badge.textContent = current + 1;
                badge.style.display = '';
            }
        };

        // Request notification permission early
        if ('Notification' in window && Notification.permission === 'default') {
            setTimeout(function() { Notification.requestPermission(); }, 5000);
        }

        // Poll for new notifications every 30 seconds
        (function() {
            var lastCount = {{ auth()->user()->unreadNotificationsCount() }};
            setInterval(function() {
                fetch('/notifications/unread-count', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var badge = document.getElementById('notification-badge');
                    if (badge) {
                        if (data.count > 0) {
                            badge.textContent = data.count;
                            badge.style.display = '';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                    if (data.count > lastCount) {
                        window._showSystemNotification('ON TRACK', 'لديك ' + (data.count - lastCount) + ' إشعار جديد', '/notifications');
                    }
                    lastCount = data.count;
                });
            }, 30000);
        })();
    </script>
    @endauth

    {{-- Firebase Push Notifications --}}
    @auth
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
        import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging.js";

        const firebaseConfig = {!! json_encode(config('firebase.web_config', [])) !!};

        if (firebaseConfig.apiKey) {
            const app = initializeApp(firebaseConfig);
            const messaging = getMessaging(app);

            // Request permission and get token
            if (Notification.permission === 'default') {
                setTimeout(function() {
                    Notification.requestPermission().then(function(permission) {
                        if (permission === 'granted') registerFCM(messaging);
                    });
                }, 3000);
            } else if (Notification.permission === 'granted') {
                registerFCM(messaging);
            }

            function registerFCM(messaging) {
                var tokenOpts = firebaseConfig.vapidKey ? { vapidKey: firebaseConfig.vapidKey } : {};
                getToken(messaging, tokenOpts).then(function(token) {
                    if (token) {
                        fetch('/api/fcm-token', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ token: token })
                        });
                    }
                }).catch(function(err) { console.log('FCM token error:', err); });
            }

            // Handle foreground messages - show system notification with sound
            onMessage(messaging, function(payload) {
                var title = payload.notification?.title || 'ON TRACK';
                var body = payload.notification?.body || '';
                var url = payload.data?.url || '/notifications';
                if (window._showSystemNotification) {
                    window._showSystemNotification(title, body, url);
                }
            });
        }
    </script>
    @endauth

    @stack('scripts')
</body>
</html>
