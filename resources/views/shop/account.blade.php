@extends('layouts.app')
@section('title', 'حسابي')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-white mb-8">حسابي</h1>

    <div class="grid md:grid-cols-3 gap-4 mb-8">
        <div class="bg-brand-dark rounded-xl p-6 text-center">
            <p class="text-3xl font-bold text-white">{{ $ordersCount }}</p>
            <p class="text-white/40 text-sm mt-1">طلباتي</p>
        </div>
        <a href="{{ route('orders.index') }}" class="bg-brand-dark rounded-xl p-6 text-center hover:bg-white/5 transition-colors">
            <p class="text-white font-medium">عرض الطلبات</p>
        </a>
        <form action="{{ route('logout') }}" method="POST" class="bg-brand-dark rounded-xl p-6 text-center hover:bg-white/5 transition-colors">
            @csrf
            <button type="submit" class="text-red-400 font-medium">تسجيل الخروج</button>
        </form>
    </div>

    {{-- Profile --}}
    <div class="bg-brand-dark rounded-xl p-6 mb-6">
        <h2 class="text-lg font-bold text-white mb-4">البيانات الشخصية</h2>
        <form action="{{ route('account.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white/70 text-sm mb-1">الاسم الأول</label>
                    <input type="text" name="first_name" value="{{ $user->first_name }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
                </div>
                <div>
                    <label class="block text-white/70 text-sm mb-1">الاسم الأخير</label>
                    <input type="text" name="last_name" value="{{ $user->last_name }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
                </div>
            </div>
            <div>
                <label class="block text-white/70 text-sm mb-1">رقم الموبايل</label>
                <input type="tel" name="phone" value="{{ $user->phone }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
            <button type="submit" class="bg-brand-red hover:bg-brand-red-dark text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">حفظ</button>
        </form>
    </div>

    {{-- Addresses --}}
    <div class="bg-brand-dark rounded-xl p-6">
        <h2 class="text-lg font-bold text-white mb-4">العناوين</h2>
        @foreach($addresses as $address)
            <div class="flex items-center justify-between py-3 border-b border-white/5 last:border-0">
                <div>
                    <p class="text-white text-sm font-medium">{{ $address->title }}</p>
                    <p class="text-white/40 text-xs">{{ $address->address }}, {{ $address->city }}, {{ $address->governorate }}</p>
                </div>
                <form action="{{ route('account.address.delete', $address) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="text-red-400 text-xs hover:underline">حذف</button>
                </form>
            </div>
        @endforeach

        <details class="mt-4">
            <summary class="text-brand-red text-sm cursor-pointer hover:underline">+ إضافة عنوان جديد</summary>
            <form action="{{ route('account.address.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <input type="text" name="title" placeholder="اسم العنوان (المنزل، العمل...)" required
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red">
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="first_name" placeholder="الاسم الأول" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red">
                    <input type="text" name="last_name" placeholder="الاسم الأخير" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red">
                </div>
                <input type="tel" name="phone" placeholder="رقم الموبايل" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red" dir="ltr">
                <input type="text" name="address" placeholder="العنوان بالتفصيل" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <select name="governorate" id="acc-gov" required onchange="updateAccCities()"
                            style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:8px 12px;color:white;font-size:13px;">
                            <option value="" style="background:#141414;">المحافظة</option>
                        </select>
                    </div>
                    <div>
                        <select name="city" id="acc-city" required
                            style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:8px 12px;color:white;font-size:13px;">
                            <option value="" style="background:#141414;">المدينة</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="bg-brand-red hover:bg-brand-red-dark text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">إضافة</button>
            </form>
        </details>
    </div>
</div>
@endsection

@push('scripts')
@include('partials.egypt-data')
<script>
const accGov=document.getElementById('acc-gov'),accCity=document.getElementById('acc-city');
if(accGov){Object.keys(egyptData).forEach(g=>{const o=document.createElement('option');o.value=g;o.textContent=g;o.style.background='#141414';accGov.appendChild(o)})}
function updateAccCities(){accCity.innerHTML='';const g=accGov.value;if(!g||!egyptData[g]){accCity.innerHTML='<option value="" style="background:#141414">المدينة</option>';return}accCity.innerHTML='<option value="" style="background:#141414">اختر المدينة</option>';egyptData[g].forEach(c=>{const o=document.createElement('option');o.value=c;o.textContent=c;o.style.background='#141414';accCity.appendChild(o)})}
</script>
@endpush
