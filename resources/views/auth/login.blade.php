<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - On Track</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{'brand-black':'#0a0a0a','brand-dark':'#141414','brand-red':'#e63946','brand-red-dark':'#c1121f'}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Cairo',sans-serif;}</style>
</head>
<body class="bg-brand-black min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}">
                <img src="/images/brand/logo.png" alt="On Track" class="h-16 w-auto mx-auto mb-2">
            </a>
            <h2 class="text-2xl font-bold text-white">تسجيل الدخول</h2>
            <p class="text-white/50 mt-2">مرحباً بعودتك!</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="bg-brand-dark rounded-2xl p-8">
            @csrf

            @if($errors->any())
                <div class="bg-red-500/10 text-red-400 p-3 rounded-lg text-sm mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-white/70 text-sm font-medium mb-1.5">البريد الإلكتروني</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                        placeholder="email@example.com" dir="ltr">
                </div>
                <div>
                    <label class="block text-white/70 text-sm font-medium mb-1.5">كلمة المرور</label>
                    <input type="password" name="password" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                        placeholder="••••••••">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-white/50 text-sm">
                        <input type="checkbox" name="remember" class="rounded">
                        تذكرني
                    </label>
                </div>
                <button type="submit"
                    class="w-full bg-brand-red hover:bg-brand-red-dark text-white font-semibold py-4 rounded-xl text-lg transition-colors">
                    تسجيل الدخول
                </button>
            </div>
            <div class="mt-6 text-center">
                <span class="text-white/40 text-sm">مالكش حساب؟ </span>
                <a href="{{ route('register') }}" class="text-brand-red text-sm font-semibold hover:underline">سجل دلوقتي</a>
            </div>
        </form>
    </div>
</body>
</html>
