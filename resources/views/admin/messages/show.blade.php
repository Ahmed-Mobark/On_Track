@extends('layouts.admin')
@section('title', 'عرض الرسالة')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.messages.index') }}" class="text-white/40 hover:text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
    <h1 class="text-2xl font-bold text-white">عرض الرسالة</h1>
</div>

<div class="bg-brand-dark rounded-xl p-6 border border-white/5 max-w-2xl space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <span class="text-white/40 text-sm">الاسم</span>
            <p class="text-white font-medium">{{ $message->name }}</p>
        </div>
        <div>
            <span class="text-white/40 text-sm">التاريخ</span>
            <p class="text-white">{{ $message->created_at->format('Y-m-d H:i') }}</p>
        </div>
        @if($message->email)
        <div>
            <span class="text-white/40 text-sm">البريد الإلكتروني</span>
            <p class="text-white" dir="ltr">{{ $message->email }}</p>
        </div>
        @endif
        @if($message->phone)
        <div>
            <span class="text-white/40 text-sm">رقم الهاتف</span>
            <p class="text-white" dir="ltr">{{ $message->phone }}</p>
        </div>
        @endif
    </div>

    <div class="border-t border-white/10 pt-4">
        <span class="text-white/40 text-sm">الموضوع</span>
        <p class="text-white font-medium">{{ $message->subject }}</p>
    </div>

    <div class="border-t border-white/10 pt-4">
        <span class="text-white/40 text-sm">الرسالة</span>
        <p class="text-white/80 mt-1 whitespace-pre-wrap">{{ $message->message }}</p>
    </div>

    <div class="border-t border-white/10 pt-4 flex gap-3">
        @if($message->email)
            <a href="mailto:{{ $message->email }}" class="bg-brand-red hover:bg-brand-red-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                رد بالإيميل
            </a>
        @endif
        @if($message->phone)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $message->phone) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                رد بالواتساب
            </a>
        @endif
        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-white/5 hover:bg-white/10 text-red-400 px-4 py-2 rounded-lg text-sm font-medium transition-colors">حذف</button>
        </form>
    </div>
</div>
@endsection
