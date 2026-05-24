<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب - On Track</title>
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
            <h2 class="text-2xl font-bold text-white">إنشاء حساب جديد</h2>
            <p class="text-white/50 mt-2">��نضم لعا��لة On Track</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="bg-brand-dark rounded-2xl p-8">
            @csrf

            @if($errors->any())
                <div class="bg-red-500/10 text-red-400 p-3 rounded-lg text-sm mb-4">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-white/70 text-sm font-medium mb-1.5">الاسم الأول</label>
                        <input type="text" name="first_name" required value="{{ old('first_name') }}"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                            placeholder="أحمد">
                    </div>
                    <div>
                        <label class="block text-white/70 text-sm font-medium mb-1.5">الاسم الأخير</label>
                        <input type="text" name="last_name" required value="{{ old('last_name') }}"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                            placeholder="محمد">
                    </div>
                </div>
                <div>
                    <label class="block text-white/70 text-sm font-medium mb-1.5">البريد الإلكتروني</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                        placeholder="email@example.com" dir="ltr">
                </div>
                <div>
                    <label class="block text-white/70 text-sm font-medium mb-1.5">رقم الموبايل</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                        placeholder="01xxxxxxxxx" dir="ltr">
                </div>
                <div>
                    <label class="block text-white/70 text-sm font-medium mb-1.5">كلمة المرور</label>
                    <input type="password" name="password" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                        placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-white/70 text-sm font-medium mb-1.5">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                        placeholder="••••••••">
                </div>
                <button type="submit"
                    class="w-full bg-brand-red hover:bg-brand-red-dark text-white font-semibold py-4 rounded-xl text-lg transition-colors">
                    إنشاء حساب
                </button>
            </div>
            <div class="mt-6 text-center">
                <span class="text-white/40 text-sm">عندك حساب؟ </span>
                <a href="{{ route('login') }}" class="text-brand-red text-sm font-semibold hover:underline">سجل دخول</a>
            </div>
        </form>
    </div>
</body>
</html>
