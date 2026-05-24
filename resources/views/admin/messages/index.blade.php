@extends('layouts.admin')
@section('title', 'الرسائل')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <h1 class="text-2xl font-bold text-white">الرسائل</h1>
        @if($unreadCount > 0)
            <span class="bg-brand-red text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadCount }} جديدة</span>
        @endif
    </div>
</div>

@if($messages->isEmpty())
    <div class="bg-brand-dark rounded-xl p-12 border border-white/5 text-center">
        <svg class="w-12 h-12 mx-auto text-white/20 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <p class="text-white/40">لا توجد رسائل حتى الآن</p>
    </div>
@else
    <div class="bg-brand-dark rounded-xl border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5 text-white/40">
                        <th class="text-right px-4 py-3 font-medium">الحالة</th>
                        <th class="text-right px-4 py-3 font-medium">الاسم</th>
                        <th class="text-right px-4 py-3 font-medium">الموضوع</th>
                        <th class="text-right px-4 py-3 font-medium">التاريخ</th>
                        <th class="text-right px-4 py-3 font-medium">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $msg)
                        <tr class="border-b border-white/5 {{ !$msg->is_read ? 'bg-white/[0.02]' : '' }}">
                            <td class="px-4 py-3">
                                @if(!$msg->is_read)
                                    <span class="w-2 h-2 bg-brand-red rounded-full inline-block"></span>
                                @else
                                    <span class="w-2 h-2 bg-white/20 rounded-full inline-block"></span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-white font-medium">{{ $msg->name }}</td>
                            <td class="px-4 py-3 text-white/60">{{ Str::limit($msg->subject, 40) }}</td>
                            <td class="px-4 py-3 text-white/40">{{ $msg->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.messages.show', $msg) }}" class="text-brand-red hover:text-brand-red-dark text-xs font-medium">عرض</a>
                                    <form action="{{ route('admin.messages.destroy', $msg) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs font-medium">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $messages->links() }}</div>
@endif
@endsection
