@extends('layouts.admin')
@section('title', 'أسعار الشحن')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">أسعار الشحن</h1>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Add Form --}}
    <div class="bg-brand-dark rounded-xl p-6 border border-white/5 h-fit">
        <h2 class="text-white font-bold mb-4">إضافة سعر شحن</h2>
        <form action="{{ route('admin.shipping.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-white/70 text-xs mb-1">المحافظة *</label>
                <select name="governorate" id="ship-gov" required onchange="updateShipCities()"
                    style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px;color:white;font-size:13px;">
                    <option value="" style="background:#141414;">اختر المحافظة</option>
                </select>
            </div>
            <div>
                <label class="block text-white/70 text-xs mb-1">المدينة (اتركه فارغ لسعر المحافظة الافتراضي)</label>
                <select name="city" id="ship-city"
                    style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px;color:white;font-size:13px;">
                    <option value="" style="background:#141414;">كل المدن (افتراضي)</option>
                </select>
            </div>
            <div>
                <label class="block text-white/70 text-xs mb-1">تكلفة الشحن (ج.م) *</label>
                <input type="number" name="cost" required step="0.01" min="0" placeholder="50"
                    style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px;color:white;font-size:13px;" dir="ltr">
            </div>
            <div>
                <label class="block text-white/70 text-xs mb-1">مدة التوصيل (أيام)</label>
                <input type="number" name="estimated_days" min="1" placeholder="3"
                    style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px;color:white;font-size:13px;" dir="ltr">
            </div>
            <button type="submit" style="width:100%;background:#e63946;color:white;font-weight:700;padding:12px;border-radius:8px;border:none;cursor:pointer;font-size:14px;transition:all 0.3s;">
                حفظ
            </button>
        </form>
        <p class="text-white/20 text-xs mt-3">* لو ما حددت مدينة، السعر ده هيكون الافتراضي لكل المحافظة</p>
    </div>

    {{-- Rates List --}}
    <div class="lg:col-span-2">
        <div class="bg-brand-dark rounded-xl border border-white/5 overflow-hidden">
            <div class="p-4 border-b border-white/5 flex items-center justify-between">
                <h2 class="text-white font-bold">الأسعار الحالية</h2>
                <span class="text-white/30 text-xs">{{ $rates->count() }} سعر</span>
            </div>

            @if($grouped->count())
                @foreach($grouped as $gov => $govRates)
                    <div class="border-b border-white/5 last:border-0">
                        <div style="padding:12px 16px;background:rgba(255,255,255,0.02);">
                            <span class="text-white font-bold text-sm">{{ $gov }}</span>
                        </div>
                        @foreach($govRates as $rate)
                            <div class="flex items-center justify-between px-4 py-3 border-t border-white/[0.03]">
                                <div>
                                    <span class="text-white/70 text-sm">{{ $rate->city ?? 'كل المدن (افتراضي)' }}</span>
                                    @if($rate->estimated_days)
                                        <span class="text-white/30 text-xs mr-2">{{ $rate->estimated_days }} يوم</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-white font-bold text-sm">{{ number_format($rate->cost) }} ج.م</span>
                                    <form action="{{ route('admin.shipping.destroy', $rate) }}" method="POST" onsubmit="return confirm('حذف؟')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-400 text-xs hover:underline">حذف</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div style="padding:40px;text-align:center;">
                    <p style="color:rgba(255,255,255,0.3);">لا توجد أسعار شحن. أضف أسعار المحافظات من النموذج</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const egyptData={"القاهرة":["مدينة نصر","المعادي","مصر الجديدة","التجمع الخامس","الشروق","العبور","بدر","حلوان","المقطم","عين شمس","شبرا"],"الجيزة":["الدقي","المهندسين","فيصل","الهرم","6 أكتوبر","الشيخ زايد","حدائق الأهرام"],"الإسكندرية":["المنتزه","شرق","وسط","غرب","العامرية","سيدي بشر","سموحة","برج العرب"],"القليوبية":["بنها","شبرا الخيمة","قليوب","الخانكة","العبور"],"الشرقية":["الزقازيق","العاشر من رمضان","بلبيس","منيا القمح"],"الدقهلية":["المنصورة","طلخا","ميت غمر","دكرنس"],"البحيرة":["دمنهور","كفر الدوار","رشيد","وادي النطرون"],"الغربية":["طنطا","المحلة الكبرى","كفر الزيات"],"المنوفية":["شبين الكوم","مدينة السادات","منوف"],"كفر الشيخ":["كفر الشيخ","دسوق","بلطيم"],"الفيوم":["الفيوم","إطسا","سنورس"],"بني سويف":["بني سويف","الواسطى","ببا"],"المنيا":["المنيا","ملوي","سمالوط"],"أسيوط":["أسيوط","ديروط","القوصية"],"سوهاج":["سوهاج","أخميم","طهطا","جرجا"],"قنا":["قنا","نجع حمادي","قوص"],"الأقصر":["الأقصر","إسنا","أرمنت"],"أسوان":["أسوان","كوم أمبو","إدفو"],"الإسماعيلية":["الإسماعيلية","فايد","التل الكبير"],"السويس":["السويس","الأربعين","عتاقة"],"بورسعيد":["بورسعيد","بورفؤاد"],"دمياط":["دمياط","رأس البر","فارسكور"],"شمال سيناء":["العريش","بئر العبد","رفح"],"جنوب سيناء":["شرم الشيخ","دهب","نويبع","طابا"],"البحر الأحمر":["الغردقة","سفاجا","مرسى علم"],"مطروح":["مرسى مطروح","العلمين","سيوة"],"الوادي الجديد":["الخارجة","الداخلة","الفرافرة"]};
const sg=document.getElementById('ship-gov'),sc=document.getElementById('ship-city');
if(sg){Object.keys(egyptData).forEach(g=>{const o=document.createElement('option');o.value=g;o.textContent=g;o.style.background='#141414';sg.appendChild(o)})}
function updateShipCities(){sc.innerHTML='<option value="" style="background:#141414">كل المدن (افتراضي)</option>';const g=sg.value;if(g&&egyptData[g])egyptData[g].forEach(c=>{const o=document.createElement('option');o.value=c;o.textContent=c;o.style.background='#141414';sc.appendChild(o)})}
</script>
@endpush
