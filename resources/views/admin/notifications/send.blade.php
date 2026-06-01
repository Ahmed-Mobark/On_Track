@extends('layouts.admin')
@section('title', 'إرسال إشعار')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.notifications.index') }}" class="text-white/40 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h1 class="text-2xl font-bold">إرسال إشعار</h1>
    </div>

    <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Target --}}
        <div>
            <label class="block text-sm text-white/70 mb-2">إرسال إلى</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="target" value="all" checked onchange="document.getElementById('user-select').classList.add('hidden')" class="accent-brand-red">
                    <span class="text-sm text-white/80">جميع العملاء</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="target" value="specific" onchange="document.getElementById('user-select').classList.remove('hidden')" class="accent-brand-red">
                    <span class="text-sm text-white/80">عميل محدد</span>
                </label>
            </div>
        </div>

        {{-- User select --}}
        <div id="user-select" class="hidden">
            <label class="block text-sm text-white/70 mb-2">اختر العميل</label>
            <select name="user_id" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm">
                <option value="">-- اختر عميل --</option>
                @foreach(\App\Models\User::where('role', 'CUSTOMER')->orderBy('first_name')->get() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('user_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Title --}}
        <div>
            <label class="block text-sm text-white/70 mb-2">عنوان الإشعار</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder-white/20"
                   placeholder="مثال: عرض خاص لفترة محدودة!">
            @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Message --}}
        <div>
            <label class="block text-sm text-white/70 mb-2">نص الإشعار</label>
            <textarea name="message" rows="4" required
                      class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder-white/20 resize-none"
                      placeholder="اكتب نص الإشعار هنا...">{{ old('message') }}</textarea>
            @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="bg-brand-red hover:bg-brand-red-dark text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
            إرسال الإشعار
        </button>
    </form>
</div>
@endsection
