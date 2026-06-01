@extends('layouts.app')
@section('title', 'الإشعارات')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">الإشعارات</h1>
        @if($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-brand-red hover:text-brand-red-dark transition-colors">تعليم الكل كمقروء</button>
            </form>
        @endif
    </div>

    <div class="space-y-2">
        @forelse($notifications as $notification)
            <div class="bg-brand-dark border border-white/10 rounded-xl p-4 transition-colors hover:bg-white/[0.02] {{ !$notification->is_read ? 'border-r-2 border-r-brand-red' : '' }}"
                 onclick="handleNotificationClick('{{ $notification->id }}', '{{ $notification->link ?? '' }}', {{ $notification->is_read ? 'true' : 'false' }}, this)"
                 style="cursor:pointer">
                <div class="flex items-start gap-3">
                    @php
                        $iconMap = [
                            'order' => ['M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'text-blue-400 bg-blue-400/10'],
                            'wallet' => ['M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'text-green-400 bg-green-400/10'],
                            'points' => ['M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'text-amber-400 bg-amber-400/10'],
                            'promo' => ['M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7', 'text-purple-400 bg-purple-400/10'],
                        ];
                        $iconData = $iconMap[$notification->type] ?? ['M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'text-white/40 bg-white/5'];
                    @endphp
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $iconData[1] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconData[0] }}"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold {{ !$notification->is_read ? 'text-white' : 'text-white/60' }}">{{ $notification->title }}</p>
                            @if(!$notification->is_read)
                                <span class="w-2 h-2 rounded-full bg-brand-red shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-sm text-white/40 mt-0.5">{{ $notification->message }}</p>
                        <p class="text-xs text-white/20 mt-1">{{ $notification->created_at ? \Carbon\Carbon::parse($notification->created_at)->diffForHumans() : '' }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20 text-white/40">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <p class="text-lg mb-1">لا يوجد إشعارات</p>
                <p class="text-sm">هتلاقي هنا كل التحديثات عن طلباتك ونقاطك ومحفظتك</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-6">{{ $notifications->links() }}</div>
    @endif
</div>

<script>
function handleNotificationClick(id, link, isRead, el) {
    if (!isRead) {
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function() {
            el.style.borderRightColor = 'transparent';
            el.style.borderRightWidth = '1px';
            var dot = el.querySelector('.bg-brand-red');
            if (dot && dot.classList.contains('w-2')) dot.remove();
            updateNotificationBadge();
            if (link) window.location.href = link;
        });
    } else if (link) {
        window.location.href = link;
    }
}

function updateNotificationBadge() {
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
    });
}
</script>
@endsection
