@extends('layouts.admin')
@section('title', 'أسعار الشحن')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">أسعار الشحن</h1>
    <form action="{{ route('admin.shipping.sync-bosta') }}" method="POST" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').textContent='جاري المزامنة...'">
        @csrf
        <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            جلب أسعار بوسطة
        </button>
    </form>
</div>

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
                            <div class="flex items-center justify-between px-4 py-3 border-t border-white/[0.03]" id="rate-row-{{ $rate->id }}">
                                {{-- Display Mode --}}
                                <div class="flex items-center justify-between w-full" id="rate-display-{{ $rate->id }}">
                                    <div>
                                        <span class="text-white/70 text-sm">{{ $rate->city ?? 'كل المدن (افتراضي)' }}</span>
                                        @if($rate->estimated_days)
                                            <span class="text-white/30 text-xs mr-2">{{ $rate->estimated_days }} يوم</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-white font-bold text-sm">{{ number_format($rate->cost) }} ج.م</span>
                                        <button type="button" onclick="startEdit('{{ $rate->id }}', {{ $rate->cost }}, {{ $rate->estimated_days ?? 'null' }})"
                                            class="text-blue-400 text-xs hover:underline">تعديل</button>
                                        <form action="{{ route('admin.shipping.destroy', $rate) }}" method="POST" onsubmit="return confirm('حذف؟')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-400 text-xs hover:underline">حذف</button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Edit Mode (hidden by default) --}}
                                <form action="{{ route('admin.shipping.update', $rate) }}" method="POST"
                                    class="hidden w-full flex items-center gap-3" id="rate-edit-{{ $rate->id }}">
                                    @csrf @method('PUT')
                                    <div class="flex-1 flex items-center gap-3">
                                        <span class="text-white/50 text-sm whitespace-nowrap">{{ $rate->city ?? 'كل المدن' }}</span>
                                        <input type="number" name="cost" step="0.01" min="0" required
                                            id="rate-cost-{{ $rate->id }}"
                                            style="width:100px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:6px;padding:6px 10px;color:white;font-size:13px;" dir="ltr">
                                        <span class="text-white/30 text-xs">ج.م</span>
                                        <input type="number" name="estimated_days" min="1" placeholder="أيام"
                                            id="rate-days-{{ $rate->id }}"
                                            style="width:80px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:6px;padding:6px 10px;color:white;font-size:13px;" dir="ltr">
                                        <span class="text-white/30 text-xs">يوم</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="submit" class="text-green-400 text-xs hover:underline">حفظ</button>
                                        <button type="button" onclick="cancelEdit('{{ $rate->id }}')" class="text-white/40 text-xs hover:underline">إلغاء</button>
                                    </div>
                                </form>
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
@include('partials.egypt-data')
<script>
// Existing rates to filter from dropdowns
const existingRates = @json($rates->map(fn($r) => ['gov' => $r->governorate, 'city' => $r->city]));

const sg = document.getElementById('ship-gov');
const sc = document.getElementById('ship-city');

if (sg) {
    Object.keys(egyptData).forEach(g => {
        const o = document.createElement('option');
        o.value = g;
        o.textContent = g;
        o.style.background = '#141414';

        // Check if this governorate has default rate AND all cities covered
        const govCities = egyptData[g];
        const govRates = existingRates.filter(r => r.gov === g);
        const hasDefault = govRates.some(r => !r.city);
        const allCitiesCovered = govCities.every(c => govRates.some(r => r.city === c));

        if (hasDefault && allCitiesCovered) {
            o.disabled = true;
            o.textContent = g + ' (مكتملة)';
            o.style.color = 'rgba(255,255,255,0.3)';
        }

        sg.appendChild(o);
    });
}

function updateShipCities() {
    sc.innerHTML = '';

    const defaultOpt = document.createElement('option');
    defaultOpt.value = '';
    defaultOpt.style.background = '#141414';

    const g = sg.value;

    // Check if governorate default already exists
    const hasDefault = existingRates.some(r => r.gov === g && !r.city);
    if (hasDefault) {
        defaultOpt.textContent = 'كل المدن (موجود بالفعل)';
        defaultOpt.disabled = true;
        sc.appendChild(defaultOpt);

        // Add a placeholder selected option
        const placeholderOpt = document.createElement('option');
        placeholderOpt.value = '';
        placeholderOpt.textContent = 'اختر مدينة';
        placeholderOpt.style.background = '#141414';
        placeholderOpt.selected = true;
        sc.insertBefore(placeholderOpt, sc.firstChild);
    } else {
        defaultOpt.textContent = 'كل المدن (افتراضي)';
        sc.appendChild(defaultOpt);
    }

    if (g && egyptData[g]) {
        egyptData[g].forEach(c => {
            const alreadyExists = existingRates.some(r => r.gov === g && r.city === c);
            if (alreadyExists) return; // Skip cities that already have rates

            const o = document.createElement('option');
            o.value = c;
            o.textContent = c;
            o.style.background = '#141414';
            sc.appendChild(o);
        });
    }
}

// Auto-fetch Bosta price when governorate changes
sg.addEventListener('change', function() {
    const gov = sg.value;
    if (!gov) return;

    const costInput = document.querySelector('input[name="cost"]');
    const daysInput = document.querySelector('input[name="estimated_days"]');

    costInput.placeholder = 'جاري الجلب...';

    fetch('{{ route("admin.shipping.bosta-cost") }}?governorate=' + encodeURIComponent(gov))
        .then(r => r.json())
        .then(data => {
            if (data.cost) {
                costInput.value = data.cost;
                costInput.placeholder = '50';
                if (!daysInput.value) daysInput.value = gov === 'الغربية' ? 1 : 3;
            } else {
                costInput.placeholder = 'غير متاح من بوسطة';
            }
        })
        .catch(() => { costInput.placeholder = '50'; });

    updateShipCities();
});

// Edit inline
function startEdit(id, cost, days) {
    document.getElementById('rate-display-' + id).classList.add('hidden');
    const editForm = document.getElementById('rate-edit-' + id);
    editForm.classList.remove('hidden');
    editForm.classList.add('flex');
    document.getElementById('rate-cost-' + id).value = cost;
    if (days) document.getElementById('rate-days-' + id).value = days;
}

function cancelEdit(id) {
    document.getElementById('rate-display-' + id).classList.remove('hidden');
    const editForm = document.getElementById('rate-edit-' + id);
    editForm.classList.add('hidden');
    editForm.classList.remove('flex');
}
</script>
@endpush
