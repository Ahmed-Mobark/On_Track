@extends('layouts.app')
@section('title', 'إتمام الشراء')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-white mb-8">إتمام الشراء</h1>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Guest Info --}}
                @if($isGuest)
                <div class="bg-brand-dark rounded-xl p-6">
                    <h2 class="text-lg font-bold text-white mb-4">بيانات التوصيل</h2>
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-white/70 text-xs mb-1">الاسم الأول *</label>
                                <input type="text" name="first_name" required value="{{ old('first_name') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                                    placeholder="أحمد">
                            </div>
                            <div>
                                <label class="block text-white/70 text-xs mb-1">الاسم الأخير *</label>
                                <input type="text" name="last_name" required value="{{ old('last_name') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                                    placeholder="محمد">
                            </div>
                        </div>
                        <div>
                            <label class="block text-white/70 text-xs mb-1">رقم الموبايل *</label>
                            <input type="tel" name="phone" required value="{{ old('phone') }}"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                                placeholder="01xxxxxxxxx" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-white/70 text-xs mb-1">البريد الإلكتروني *</label>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                                placeholder="email@example.com" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-white/70 text-xs mb-1">العنوان بالتفصيل *</label>
                            <input type="text" name="address" required value="{{ old('address') }}"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                                placeholder="شارع، مبنى، شقة...">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-white/70 text-xs mb-1">المحافظة *</label>
                                <select name="governorate" id="governorate-select" required onchange="updateCities()"
                                    style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px 16px;color:white;font-size:14px;">
                                    <option value="" style="background:#141414;">اختر المحافظة</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-white/70 text-xs mb-1">المدينة *</label>
                                <select name="city" id="city-select" required
                                    style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px 16px;color:white;font-size:14px;">
                                    <option value="" style="background:#141414;">اختر المحافظة أولاً</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <p class="text-white/30 text-xs mt-3">
                        عندك حساب؟ <a href="{{ route('login') }}" class="text-brand-red hover:underline">سجل دخول</a>
                    </p>
                </div>
                @else
                {{-- Logged in: pick saved address --}}
                <div class="bg-brand-dark rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-white">عنوان التوصيل</h2>
                        <button type="button" onclick="document.getElementById('new-address-panel').classList.toggle('hidden')"
                            style="display:flex;align-items:center;gap:4px;color:#e63946;font-size:13px;background:none;border:none;cursor:pointer;font-weight:600;">
                            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            عنوان جديد
                        </button>
                    </div>

                    {{-- Existing addresses --}}
                    <div id="addresses-list">
                        @if($addresses->count())
                            <div class="space-y-2">
                                @foreach($addresses as $address)
                                    <label class="address-option flex items-center gap-3 p-3 rounded-lg border border-white/10 cursor-pointer hover:border-brand-red transition-colors">
                                        <input type="radio" name="address_id" value="{{ $address->id }}" {{ $loop->first ? 'checked' : '' }}
                                            onchange="document.getElementById('governorate-hidden').value='{{ $address->governorate }}';document.getElementById('city-hidden').value='{{ $address->city }}';fetchShippingForAddress('{{ $address->governorate }}','{{ $address->city }}')">
                                        <div>
                                            <p class="text-white text-sm font-medium">{{ $address->title }}</p>
                                            <p class="text-white/40 text-xs">{{ $address->address }}, {{ $address->city }}, {{ $address->governorate }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-white/40 text-sm" id="no-addresses-msg">لا يوجد عناوين. اضغط "عنوان جديد" لإضافة عنوان</p>
                        @endif
                    </div>

                    {{-- Inline new address form --}}
                    <div id="new-address-panel" class="hidden mt-4 pt-4 border-t border-white/10">
                        <div class="space-y-3">
                            <input type="text" id="new-addr-title" placeholder="اسم العنوان (المنزل، العمل...)"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red">
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" id="new-addr-fname" placeholder="الاسم الأول"
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                                    value="{{ auth()->user()->first_name }}">
                                <input type="text" id="new-addr-lname" placeholder="الاسم الأخير"
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                                    value="{{ auth()->user()->last_name }}">
                            </div>
                            <input type="tel" id="new-addr-phone" placeholder="رقم الموبايل" dir="ltr"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                                value="{{ auth()->user()->phone }}">
                            <input type="text" id="new-addr-address" placeholder="العنوان بالتفصيل (شارع، مبنى، شقة...)"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red">
                            <div class="grid grid-cols-2 gap-3">
                                <select id="new-addr-gov" onchange="updateNewAddrCities()"
                                    style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px 16px;color:white;font-size:14px;">
                                    <option value="" style="background:#141414;">المحافظة</option>
                                </select>
                                <select id="new-addr-city"
                                    style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px 16px;color:white;font-size:14px;">
                                    <option value="" style="background:#141414;">المدينة</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" onclick="saveNewAddress()"
                                    style="flex:1;background:#e63946;color:white;font-weight:700;padding:12px;border-radius:10px;font-size:14px;border:none;cursor:pointer;transition:all 0.3s;">
                                    حفظ العنوان
                                </button>
                                <button type="button" onclick="document.getElementById('new-address-panel').classList.add('hidden')"
                                    style="padding:12px 20px;background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5);border-radius:10px;font-size:14px;border:1px solid rgba(255,255,255,0.1);cursor:pointer;">
                                    إلغاء
                                </button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="governorate-hidden" name="selected_governorate">
                    <input type="hidden" id="city-hidden" name="selected_city">
                </div>
                @endif

                {{-- Payment --}}
                <div class="bg-brand-dark rounded-xl p-6">
                    <h2 class="text-lg font-bold text-white mb-4">طريقة الدفع</h2>
                    <div class="space-y-2">
                        @foreach(['COD' => 'الدفع عند الاستلام', 'VISA' => 'فيزا / ماستركارد', 'INSTAPAY' => 'انستاباي', 'WALLET' => 'محفظة إلكترونية'] as $method => $label)
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-white/10 cursor-pointer hover:border-brand-red transition-colors">
                                <input type="radio" name="payment_method" value="{{ $method }}" {{ $loop->first ? 'checked' : '' }}>
                                <span class="text-white text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Coupon --}}
                <div class="bg-brand-dark rounded-xl p-6">
                    <h2 class="text-lg font-bold text-white mb-4">كوبون خصم</h2>
                    <div class="flex gap-2">
                        <input type="text" name="coupon_code" id="coupon-input" placeholder="أدخل كود الخصم"
                            class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red text-right">
                        <button type="button" id="apply-coupon-btn" onclick="applyCoupon()"
                            class="bg-brand-red hover:bg-brand-red-dark text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                            تطبيق
                        </button>
                    </div>
                    <div id="coupon-result" class="hidden mt-3 flex items-center justify-between bg-green-500/10 border border-green-500/20 rounded-lg px-4 py-2.5">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-green-400 text-sm font-medium" id="coupon-label"></span>
                        </div>
                        <button type="button" onclick="removeCoupon()" class="text-white/40 hover:text-white text-xs">إزالة</button>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-brand-dark rounded-xl p-6">
                    <h2 class="text-lg font-bold text-white mb-4">ملاحظات</h2>
                    <textarea name="notes" rows="3" placeholder="ملاحظات إضافية (اختياري)"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red resize-none"></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-brand-red hover:bg-brand-red-dark text-white font-semibold py-4 rounded-xl text-lg transition-colors">
                    تأكيد الطلب
                </button>
            </form>
        </div>

        {{-- Order Summary --}}
        <div>
            <div class="bg-brand-dark rounded-xl p-6 sticky top-20">
                <h2 class="text-lg font-bold text-white mb-4">ملخص الطلب</h2>
                <div class="space-y-3 mb-4">
                    @foreach($items as $item)
                        <div class="flex items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-white text-xs line-clamp-1">{{ $item['product']->name_ar ?? $item['product']->name }}</p>
                                <p class="text-white/40 text-[10px]">{{ $item['variant']->size }}/{{ $item['variant']->color }} × {{ $item['quantity'] }}</p>
                            </div>
                            <span class="text-white text-xs font-medium">{{ number_format($item['price'] * $item['quantity']) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-white/10 pt-3 space-y-2 text-sm">
                    <div class="flex justify-between text-white/60">
                        <span>المجموع الفرعي</span>
                        <span>{{ number_format($subtotal) }} ج.م</span>
                    </div>
                    <div class="flex justify-between text-white/60">
                        <span>الشحن</span>
                        <span id="shipping-cost-display">{{ $shippingCost > 0 ? number_format($shippingCost) . ' ج.م' : 'يحسب بعد اختيار المحافظة' }}</span>
                    </div>
                    <div id="discount-row" class="hidden flex justify-between text-green-400">
                        <span>الخصم</span>
                        <span id="discount-display">0 ج.م</span>
                    </div>
                    <div class="flex justify-between text-white font-bold text-lg pt-2 border-t border-white/10">
                        <span>الإجمالي</span>
                        <span id="total-display">{{ number_format($total) }} ج.م</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('partials.egypt-data')
@push('scripts')
<script>
// Populate governorates
const govSelect = document.getElementById('governorate-select');
const citySelect = document.getElementById('city-select');

if (govSelect) {
    Object.keys(egyptData).forEach(gov => {
        const opt = document.createElement('option');
        opt.value = gov;
        opt.textContent = gov;
        opt.style.background = '#141414';
        @if(old('governorate'))
        if (gov === '{{ old("governorate") }}') opt.selected = true;
        @endif
        govSelect.appendChild(opt);
    });

    // If old value exists, load cities
    @if(old('governorate'))
    updateCities();
    @endif
}

function updateCities() {
    const gov = govSelect.value;
    citySelect.innerHTML = '';

    if (!gov || !egyptData[gov]) {
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'اختر المحافظة أولاً';
        opt.style.background = '#141414';
        citySelect.appendChild(opt);
        return;
    }

    const defaultOpt = document.createElement('option');
    defaultOpt.value = '';
    defaultOpt.textContent = 'اختر المدينة';
    defaultOpt.style.background = '#141414';
    citySelect.appendChild(defaultOpt);

    egyptData[gov].forEach(city => {
        const opt = document.createElement('option');
        opt.value = city;
        opt.textContent = city;
        opt.style.background = '#141414';
        @if(old('city'))
        if (city === '{{ old("city") }}') opt.selected = true;
        @endif
        citySelect.appendChild(opt);
    });
}

// Update shipping cost dynamically
const subtotal = {{ $subtotal }};

function fetchShippingCost() {
    const gov = govSelect ? govSelect.value : '';
    const city = citySelect ? citySelect.value : '';
    if (!gov) return;

    fetch(`{{ route('api.shipping.cost') }}?governorate=${encodeURIComponent(gov)}&city=${encodeURIComponent(city)}`)
        .then(r => r.json())
        .then(data => {
            const cost = data.cost;
            const total = subtotal + cost;
            const shipEl = document.getElementById('shipping-cost-display');
            const totalEl = document.getElementById('total-display');
            if (shipEl) shipEl.textContent = cost.toLocaleString() + ' ج.م';
            if (totalEl) totalEl.textContent = total.toLocaleString() + ' ج.م';
        });
}

// Hook into governorate/city change
const origUpdateCities = updateCities;
updateCities = function() {
    origUpdateCities();
    fetchShippingCost();
};
if (citySelect) citySelect.addEventListener('change', fetchShippingCost);

// --- New address inline form (for logged-in users) ---
var newAddrGov = document.getElementById('new-addr-gov');
var newAddrCity = document.getElementById('new-addr-city');

if (newAddrGov) {
    Object.keys(egyptData).forEach(function(g) {
        var o = document.createElement('option');
        o.value = g; o.textContent = g; o.style.background = '#141414';
        newAddrGov.appendChild(o);
    });
}

function updateNewAddrCities() {
    if (!newAddrCity) return;
    newAddrCity.innerHTML = '';
    var g = newAddrGov.value;
    if (!g || !egyptData[g]) {
        newAddrCity.innerHTML = '<option value="" style="background:#141414">المدينة</option>';
        return;
    }
    newAddrCity.innerHTML = '<option value="" style="background:#141414">اختر المدينة</option>';
    egyptData[g].forEach(function(c) {
        var o = document.createElement('option');
        o.value = c; o.textContent = c; o.style.background = '#141414';
        newAddrCity.appendChild(o);
    });
}

function fetchShippingForAddress(gov, city) {
    if (!gov) return;
    fetch('/api/shipping-cost?governorate=' + encodeURIComponent(gov) + '&city=' + encodeURIComponent(city))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var shipEl = document.getElementById('shipping-cost-display');
            var totalEl = document.getElementById('total-display');
            if (shipEl) shipEl.textContent = data.cost.toLocaleString() + ' ج.م';
            if (totalEl) totalEl.textContent = (subtotal + data.cost).toLocaleString() + ' ج.م';
        });
}

function saveNewAddress() {
    var title = document.getElementById('new-addr-title').value.trim();
    var fname = document.getElementById('new-addr-fname').value.trim();
    var lname = document.getElementById('new-addr-lname').value.trim();
    var phone = document.getElementById('new-addr-phone').value.trim();
    var addr = document.getElementById('new-addr-address').value.trim();
    var gov = newAddrGov ? newAddrGov.value : '';
    var city = newAddrCity ? newAddrCity.value : '';

    if (!title || !fname || !lname || !phone || !addr || !gov || !city) {
        showToast('يرجى ملء جميع الحقول');
        return;
    }

    var fd = new FormData();
    fd.append('title', title);
    fd.append('first_name', fname);
    fd.append('last_name', lname);
    fd.append('phone', phone);
    fd.append('address', addr);
    fd.append('governorate', gov);
    fd.append('city', city);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('/account/address', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.id) {
            // Add new address to the list dynamically
            var list = document.getElementById('addresses-list');
            var noMsg = document.getElementById('no-addresses-msg');
            if (noMsg) noMsg.remove();

            // Find or create the space-y-2 container
            var container = list.querySelector('.space-y-2');
            if (!container) {
                container = document.createElement('div');
                container.className = 'space-y-2';
                list.appendChild(container);
            }

            // Uncheck all existing radios
            container.querySelectorAll('input[type="radio"]').forEach(function(r) { r.checked = false; });

            var label = document.createElement('label');
            label.className = 'address-option flex items-center gap-3 p-3 rounded-lg border border-brand-red cursor-pointer transition-colors';
            label.innerHTML = '<input type="radio" name="address_id" value="' + data.id + '" checked>' +
                '<div><p class="text-white text-sm font-medium">' + data.title + '</p>' +
                '<p class="text-white/40 text-xs">' + data.address + ', ' + data.city + ', ' + data.governorate + '</p></div>';

            label.querySelector('input').addEventListener('change', function() {
                fetchShippingForAddress(data.governorate, data.city);
            });

            container.appendChild(label);

            // Hide form & update shipping
            document.getElementById('new-address-panel').classList.add('hidden');
            fetchShippingForAddress(data.governorate, data.city);
            showToast('تم إضافة العنوان بنجاح');

            // Clear form
            document.getElementById('new-addr-title').value = '';
            document.getElementById('new-addr-address').value = '';
            if (newAddrGov) newAddrGov.value = '';
            if (newAddrCity) newAddrCity.innerHTML = '<option value="" style="background:#141414">المدينة</option>';
        } else if (data.errors) {
            var msgs = Object.values(data.errors).flat();
            showToast(msgs[0] || 'حدث خطأ');
        }
    })
    .catch(function() {
        showToast('حدث خطأ، حاول مرة أخرى');
    });
}
// Coupon
let appliedDiscount = 0;

function applyCoupon() {
    const code = document.getElementById('coupon-input').value.trim();
    if (!code) { showToast('أدخل كود الخصم'); return; }

    const btn = document.getElementById('apply-coupon-btn');
    btn.disabled = true;
    btn.textContent = 'جاري التحقق...';

    const fd = new FormData();
    fd.append('code', code);
    fd.append('subtotal', subtotal);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('{{ route("api.coupon.validate") }}', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.valid) {
            appliedDiscount = data.discount;
            document.getElementById('coupon-result').classList.remove('hidden');
            document.getElementById('coupon-label').textContent = 'خصم ' + data.label;
            document.getElementById('coupon-input').readOnly = true;
            document.getElementById('coupon-input').style.opacity = '0.5';
            btn.classList.add('hidden');

            // Update summary
            document.getElementById('discount-row').classList.remove('hidden');
            document.getElementById('discount-row').style.display = 'flex';
            document.getElementById('discount-display').textContent = '-' + data.discount.toLocaleString() + ' ج.م';
            updateTotal();
        } else {
            showToast(data.error || 'كود الخصم غير صحيح');
        }
    })
    .catch(() => showToast('حدث خطأ، حاول مرة أخرى'))
    .finally(() => { btn.disabled = false; btn.textContent = 'تطبيق'; });
}

function removeCoupon() {
    appliedDiscount = 0;
    document.getElementById('coupon-result').classList.add('hidden');
    document.getElementById('coupon-input').readOnly = false;
    document.getElementById('coupon-input').style.opacity = '1';
    document.getElementById('coupon-input').value = '';
    document.getElementById('apply-coupon-btn').classList.remove('hidden');
    document.getElementById('discount-row').classList.add('hidden');
    document.getElementById('discount-row').style.display = 'none';
    updateTotal();
}

function updateTotal() {
    const shipText = document.getElementById('shipping-cost-display').textContent;
    const shipCost = parseInt(shipText.replace(/[^0-9]/g, '')) || 0;
    const total = subtotal - appliedDiscount + shipCost;
    document.getElementById('total-display').textContent = total.toLocaleString() + ' ج.م';
}
</script>
@endpush
