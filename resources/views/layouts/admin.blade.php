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
                    ['route' => 'admin.notifications.index', 'label' => 'الإشعارات', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
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
            @php $adminUnread = auth()->user()->unreadNotificationsCount(); @endphp
            <a href="{{ route('admin.notifications.index') }}" class="relative text-white/60 hover:text-white p-2 rounded-lg hover:bg-white/5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                @if($adminUnread > 0)
                    <span class="absolute top-0.5 right-0.5 bg-brand-red text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center font-bold leading-none">{{ $adminUnread > 9 ? '9+' : $adminUnread }}</span>
                @endif
            </a>
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

    {{-- AI Assistant Chat Widget --}}
    <div id="ai-chat-btn" onclick="toggleAiChat()"
        style="position:fixed;bottom:24px;left:24px;z-index:60;width:56px;height:56px;background:#e63946;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 20px rgba(230,57,70,0.4);transition:all 0.3s;">
        <svg id="ai-chat-icon" style="width:24px;height:24px;" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        <svg id="ai-close-icon" style="width:24px;height:24px;display:none;" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </div>

    <div id="ai-chat-panel" style="display:none;position:fixed;bottom:92px;left:24px;z-index:60;width:380px;max-width:calc(100vw - 48px);background:#141414;border:1px solid rgba(255,255,255,0.1);border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.5);overflow:hidden;">
        {{-- Header --}}
        <div style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,0.1);display:flex;align-items:center;gap:10px;">
            <div style="width:32px;height:32px;background:rgba(230,57,70,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <svg style="width:16px;height:16px;" fill="none" stroke="#e63946" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div>
                <p style="color:white;font-size:14px;font-weight:700;margin:0;">مساعد ON TRACK</p>
                <p style="color:rgba(255,255,255,0.4);font-size:11px;margin:0;">اسألني عن أي حاجة في السيستم</p>
            </div>
        </div>

        {{-- Messages --}}
        <div id="ai-messages" style="height:350px;overflow-y:auto;padding:16px 20px;display:flex;flex-direction:column;gap:12px;">
            <div class="ai-msg bot">
                <div style="background:rgba(255,255,255,0.05);border-radius:12px 12px 12px 4px;padding:10px 14px;max-width:90%;color:rgba(255,255,255,0.8);font-size:13px;line-height:1.6;">
                    أهلاً! أنا مساعدك الذكي. اضغط على أي زرار أو اكتب سؤالك:
                    <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:5px;" id="quick-actions">
                        <span onclick="askQuick('ملخص عام')" class="ai-quick-btn">ملخص عام</span>
                        <span onclick="askQuick('الطلبات المعلقة')" class="ai-quick-btn">المعلقة</span>
                        <span onclick="askQuick('اخر طلب')" class="ai-quick-btn">آخر طلب</span>
                        <span onclick="askQuick('طلبات النهاردة')" class="ai-quick-btn">النهاردة</span>
                        <span onclick="askQuick('الايرادات')" class="ai-quick-btn">الإيرادات</span>
                        <span onclick="askQuick('في انتظار تاكيد الدفع')" class="ai-quick-btn">تأكيد دفع</span>
                        <span onclick="askQuick('اكتر منتج مبيعا')" class="ai-quick-btn">أكتر منتج</span>
                        <span onclick="askQuick('نفذ من المخزون')" class="ai-quick-btn">نفذ</span>
                        <span onclick="askQuick('اكتر عميل اشتري')" class="ai-quick-btn">أكتر عميل</span>
                    </div>
                    <style>.ai-quick-btn{background:rgba(230,57,70,0.1);color:#e63946;padding:5px 12px;border-radius:20px;font-size:11px;cursor:pointer;border:1px solid rgba(230,57,70,0.2);transition:all 0.2s;display:inline-block;}.ai-quick-btn:hover{background:rgba(230,57,70,0.25);}</style>
                </div>
            </div>
        </div>

        {{-- Input --}}
        <div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,0.1);display:flex;gap:8px;">
            <input type="text" id="ai-input" placeholder="اسأل سؤالك هنا..."
                style="flex:1;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:10px;padding:10px 14px;color:white;font-size:13px;outline:none;font-family:'Cairo',sans-serif;"
                onkeydown="if(event.key==='Enter')sendAiMessage()">
            <button onclick="sendAiMessage()"
                style="background:#e63946;border:none;border-radius:10px;padding:10px 16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <svg style="width:18px;height:18px;transform:rotate(180deg);" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleAiChat() {
            var panel = document.getElementById('ai-chat-panel');
            var iconChat = document.getElementById('ai-chat-icon');
            var iconClose = document.getElementById('ai-close-icon');
            if (panel.style.display === 'none') {
                panel.style.display = 'block';
                iconChat.style.display = 'none';
                iconClose.style.display = 'block';
                document.getElementById('ai-input').focus();
            } else {
                panel.style.display = 'none';
                iconChat.style.display = 'block';
                iconClose.style.display = 'none';
            }
        }

        function askQuick(q) {
            document.getElementById('ai-input').value = q;
            sendAiMessage();
        }

        function addMessage(text, isUser, actions) {
            var container = document.getElementById('ai-messages');
            var div = document.createElement('div');
            div.className = 'ai-msg ' + (isUser ? 'user' : 'bot');

            var bubble = document.createElement('div');
            bubble.style.cssText = isUser
                ? 'background:#e63946;border-radius:12px 12px 4px 12px;padding:10px 14px;max-width:85%;color:white;font-size:13px;line-height:1.6;margin-right:auto;'
                : 'background:rgba(255,255,255,0.05);border-radius:12px 12px 12px 4px;padding:10px 14px;max-width:95%;color:rgba(255,255,255,0.8);font-size:13px;line-height:1.6;white-space:pre-line;';
            bubble.textContent = text;

            // Add action buttons
            if (actions && actions.length > 0) {
                var actionsDiv = document.createElement('div');
                actionsDiv.style.cssText = 'margin-top:10px;display:flex;flex-wrap:wrap;gap:6px;';
                actions.forEach(function(action) {
                    var btn = document.createElement('a');
                    btn.href = action.url;
                    btn.textContent = action.label;
                    if (action.target) btn.target = action.target;
                    var bgColor = action.style === 'green' ? 'rgba(34,197,94,0.15)' : action.style === 'blue' ? 'rgba(59,130,246,0.15)' : 'rgba(230,57,70,0.15)';
                    var txtColor = action.style === 'green' ? '#22c55e' : action.style === 'blue' ? '#3b82f6' : '#e63946';
                    var borderColor = action.style === 'green' ? 'rgba(34,197,94,0.3)' : action.style === 'blue' ? 'rgba(59,130,246,0.3)' : 'rgba(230,57,70,0.3)';
                    btn.style.cssText = 'background:' + bgColor + ';color:' + txtColor + ';padding:5px 12px;border-radius:8px;font-size:11px;text-decoration:none;border:1px solid ' + borderColor + ';font-weight:600;transition:all 0.2s;font-family:Cairo,sans-serif;';
                    actionsDiv.appendChild(btn);
                });
                bubble.appendChild(actionsDiv);
            }

            div.appendChild(bubble);
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }

        function sendAiMessage() {
            var input = document.getElementById('ai-input');
            var question = input.value.trim();
            if (!question) return;

            addMessage(question, true);
            input.value = '';

            var loadingId = 'loading-' + Date.now();
            var container = document.getElementById('ai-messages');
            var loadDiv = document.createElement('div');
            loadDiv.id = loadingId;
            loadDiv.innerHTML = '<div style="background:rgba(255,255,255,0.05);border-radius:12px;padding:10px 14px;color:rgba(255,255,255,0.4);font-size:13px;display:flex;align-items:center;gap:8px;"><span style="display:inline-block;width:6px;height:6px;background:#e63946;border-radius:50%;animation:pulse 1s infinite;"></span> جاري البحث...</div>';
            container.appendChild(loadDiv);
            container.scrollTop = container.scrollHeight;

            fetch('{{ route("admin.ai-assistant") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ question: question })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var el = document.getElementById(loadingId);
                if (el) el.remove();
                addMessage(data.answer || 'حدث خطأ', false, data.actions || null);
            })
            .catch(function() {
                var el = document.getElementById(loadingId);
                if (el) el.remove();
                addMessage('حدث خطأ في الاتصال، حاول مرة أخرى', false);
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
