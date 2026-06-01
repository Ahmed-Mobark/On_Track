@extends('layouts.admin')
@section('title', 'الإشعارات')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">الإشعارات</h1>
    <div class="flex items-center gap-3">
        <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm text-white/60 hover:text-white transition-colors">تعليم الكل كمقروء</button>
        </form>
        <a href="{{ route('admin.notifications.send') }}" class="bg-brand-red hover:bg-brand-red-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            إرسال إشعار
        </a>
    </div>
</div>

<div class="space-y-2">
    @forelse($notifications as $notification)
        <a href="{{ $notification->link ?? '#' }}" class="block bg-brand-dark border border-white/10 rounded-xl p-4 hover:bg-white/[0.02] transition-colors {{ !$notification->is_read ? 'border-r-2 border-r-brand-red' : '' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    @php
                        $iconMap = [
                            'order' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                            'inventory' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                            'wallet' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                            'points' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                        ];
                        $icon = $iconMap[$notification->type] ?? 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9';
                        $colorMap = ['order' => 'text-blue-400', 'inventory' => 'text-yellow-400', 'wallet' => 'text-green-400', 'points' => 'text-amber-400'];
                        $color = $colorMap[$notification->type] ?? 'text-white/60';
                    @endphp
                    <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center shrink-0 {{ $color }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold {{ !$notification->is_read ? 'text-white' : 'text-white/70' }}">{{ $notification->title }}</p>
                        <p class="text-sm text-white/50 mt-0.5 whitespace-pre-line">{{ $notification->message }}</p>
                    </div>
                </div>
                <span class="text-xs text-white/30 shrink-0">{{ $notification->created_at ? \Carbon\Carbon::parse($notification->created_at)->diffForHumans() : '' }}</span>
            </div>
        </a>
    @empty
        <div class="text-center py-20 text-white/40">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <p>لا يوجد إشعارات</p>
        </div>
    @endforelse
</div>

@if($notifications->hasPages())
    <div class="mt-6">{{ $notifications->links() }}</div>
@endif
@endsection
