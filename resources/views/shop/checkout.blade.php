@extends('layouts.app')
@section('title', 'إتمام الشراء')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('cart') }}" class="text-white/40 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-white">إتمام الشراء</h1>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 lg:gap-8">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="checkout-form">
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

                {{-- Payment Options --}}
                <div class="bg-brand-dark rounded-xl p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <h2 class="text-lg font-bold text-white">طريقة الدفع</h2>
                    </div>

                    <input type="hidden" name="payment_method" value="INSTAPAY">
                    <input type="hidden" name="deposit_amount" id="deposit-amount-input" value="{{ $shippingCost > 0 ? $shippingCost : $depositAmount }}">

                    <div class="space-y-3" id="payment-options">
                        {{-- Option 1: Pay Partial (Shipping or Deposit) --}}
                        <label class="payment-option relative block p-4 rounded-xl border-2 border-brand-red bg-brand-red/5 cursor-pointer transition-all" id="option-shipping">
                            <input type="radio" name="payment_type" value="SHIPPING_ONLY" class="hidden" checked>
                            {{-- Badge --}}
                            <span class="absolute -top-2.5 right-4 bg-brand-red text-white text-[10px] font-bold px-3 py-0.5 rounded-full shadow-lg">
                                الأكثر شيوعاً
                            </span>
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded-full border-2 border-brand-red flex items-center justify-center mt-0.5 shrink-0">
                                    <div class="w-2.5 h-2.5 rounded-full bg-brand-red option-dot"></div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-white font-bold text-sm mb-1" id="partial-title">{{ $shippingCost > 0 ? 'ادفع الشحن فقط' : 'ادفع عربون تأكيد' }}</h3>
                                    <p class="text-white/50 text-xs leading-relaxed mb-3" id="partial-desc">{{ $shippingCost > 0 ? 'ادفع رسوم الشحن الآن لتأكيد طلبك. المبلغ المتبقي يُدفع عند الاستلام.' : 'ادفع عربون بسيط لتأكيد جدية طلبك. المبلغ المتبقي يُدفع عند الاستلام.' }}</p>
                                    <div class="bg-white/5 rounded-lg p-3 space-y-1.5">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-white/60" id="partial-fee-label">{{ $shippingCost > 0 ? 'رسوم الشحن' : 'عربون التأكيد' }}</span>
                                            <span class="text-brand-red font-bold" id="shipping-pay-amount">{{ $shippingCost > 0 ? number_format($shippingCost) . ' ج.م' : number_format($depositAmount) . ' ج.م' }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-white/60">المبلغ المتبقي (عند الاستلام)</span>
                                            <span class="text-white font-medium" id="remaining-amount">{{ $shippingCost > 0 ? number_format($subtotal - ($discount ?? 0)) . ' ج.م' : number_format($subtotal - $depositAmount) . ' ج.م' }}</span>
                                        </div>
                                    </div>
                                    <p id="free-shipping-msg" class="text-green-400/70 text-[10px] mt-2 flex items-center gap-1 {{ $isFreeShipping ? '' : 'hidden' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        الشحن مجاني! العربون يُخصم من إجمالي الطلب
                                    </p>
                                </div>
                            </div>
                        </label>

                        {{-- Option 2: Pay Full Amount --}}
                        <label class="payment-option relative block p-4 rounded-xl border-2 border-white/10 cursor-pointer transition-all hover:border-white/30" id="option-full">
                            <input type="radio" name="payment_type" value="FULL" class="hidden">
                            {{-- Badge --}}
                            <span class="absolute -top-2.5 right-4 bg-green-500 text-white text-[10px] font-bold px-3 py-0.5 rounded-full shadow-lg">
                                أفضل قيمة
                            </span>
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 rounded-full border-2 border-white/30 flex items-center justify-center mt-0.5 shrink-0">
                                    <div class="w-2.5 h-2.5 rounded-full bg-transparent option-dot"></div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-white font-bold text-sm mb-1">ادفع المبلغ كاملاً</h3>
                                    <p class="text-white/50 text-xs leading-relaxed mb-3">ادفع قيمة الطلب كاملة الآن واستمتع بتأكيد أسرع لطلبك.</p>
                                    <div class="bg-white/5 rounded-lg p-3">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-white/60">إجمالي المبلغ</span>
                                            <span class="text-green-400 font-bold" id="full-pay-amount">{{ number_format($total) }} ج.م</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- InstaPay Payment Area --}}
                <div class="bg-brand-dark rounded-xl p-6" id="instapay-section">
                    <div class="text-center mb-5">
                        <div class="inline-flex items-center gap-2 bg-white/5 rounded-full px-4 py-2 mb-3">
                            <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                            <span class="text-white/70 text-xs font-medium">ادفع عبر InstaPay</span>
                        </div>
                        <p class="text-white/40 text-xs">امسح الكود أو حوّل على الرقم أدناه</p>
                    </div>

                    {{-- QR Code --}}
                    <div class="flex justify-center mb-5">
                        <div class="bg-white rounded-2xl p-4 shadow-2xl">
                            <img src="{{ asset('images/instapay-qr.png') }}" alt="InstaPay QR Code"
                                class="w-48 h-48 object-contain"
                                onerror="this.parentElement.innerHTML='<div class=\'w-48 h-48 flex items-center justify-center text-gray-400 text-sm\'>QR Code</div>'">
                        </div>
                    </div>

                    {{-- InstaPay Details --}}
                    <div class="bg-white/5 rounded-xl p-4 mb-4">
                        <div class="text-center space-y-2">
                            <p class="text-white/50 text-xs">حساب InstaPay</p>
                            <p class="text-white font-bold text-lg tracking-wide" dir="ltr" id="instapay-number">{{ \App\Models\SiteSetting::get('instapay_number', config('app.instapay_number', '01XXXXXXXXX')) }}</p>
                            <p class="text-white/50 text-xs">{{ \App\Models\SiteSetting::get('instapay_name', config('app.instapay_name', 'ON TRACK Store')) }}</p>
                        </div>
                    </div>

                    {{-- Copy Button --}}
                    <button type="button" onclick="copyInstapayNumber()" id="copy-btn"
                        class="w-full flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl py-3 text-white text-sm font-medium transition-all mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span id="copy-text">نسخ رقم InstaPay</span>
                    </button>

                    {{-- Amount to Pay Display --}}
                    <div class="bg-brand-red/10 border border-brand-red/30 rounded-xl p-4 mb-4 text-center">
                        <p class="text-white/60 text-xs mb-1">المبلغ المطلوب تحويله</p>
                        <p class="text-brand-red text-2xl font-black" id="amount-to-pay">{{ $shippingCost > 0 ? number_format($shippingCost) . ' ج.م' : '---' }}</p>
                    </div>

                    {{-- Upload Payment Proof --}}
                    <div class="space-y-3">
                        <label class="block">
                            <span class="text-white/70 text-xs mb-2 block">ارفع صورة إثبات الدفع (سكرين شوت التحويل) *</span>
                            <div class="relative">
                                <input type="file" name="payment_proof" id="payment-proof-input" accept="image/*" required
                                    class="hidden" onchange="handleFileUpload(this)">
                                <div id="upload-area" onclick="document.getElementById('payment-proof-input').click()"
                                    class="w-full border-2 border-dashed border-white/20 hover:border-brand-red/50 rounded-xl p-6 text-center cursor-pointer transition-all">
                                    <div id="upload-placeholder">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-white/40 text-sm">اضغط لرفع صورة التحويل</p>
                                        <p class="text-white/20 text-xs mt-1">JPG, PNG - حد أقصى 5MB</p>
                                    </div>
                                    <div id="upload-preview" class="hidden">
                                        <img id="preview-image" class="max-h-32 mx-auto rounded-lg mb-2" alt="Preview">
                                        <p class="text-green-400 text-xs font-medium" id="file-name"></p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Trust Elements --}}
                <div class="bg-gradient-to-b from-green-500/5 to-transparent border border-green-500/10 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="text-green-400 text-sm font-bold">دفع آمن ومحمي</span>
                    </div>
                    <div class="space-y-2">
                        <p class="text-white/50 text-xs leading-relaxed flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 text-green-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            يتم تسجيل والتحقق من عملية الدفع بالكامل قبل معالجة الطلب.
                        </p>
                        <p class="text-white/50 text-xs leading-relaxed flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 text-green-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            في حالة عدم إمكانية تنفيذ طلبك، يتم استرداد المبلغ وفقاً لسياسة الاسترداد.
                        </p>
                    </div>
                </div>

                {{-- Wallet Balance --}}
                @if(!$isGuest && $walletBalance > 0)
                <div class="bg-brand-dark rounded-xl p-6" id="wallet-section">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <h2 class="text-lg font-bold text-white">المحفظة</h2>
                        <span class="bg-green-500/10 text-green-400 text-xs font-bold px-2.5 py-0.5 rounded-full mr-auto">{{ number_format($walletBalance) }} ج.م</span>
                    </div>

                    <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all" id="wallet-toggle-label"
                        style="border-color:rgba(255,255,255,0.1);background:transparent;">
                        <input type="checkbox" id="use-wallet-check" onchange="toggleWallet()" class="w-4 h-4 accent-green-500">
                        <input type="hidden" name="use_wallet" id="use-wallet-input" value="0">
                        <div class="flex-1">
                            <span class="text-white text-sm font-medium">استخدام رصيد المحفظة في هذا الطلب</span>
                            <p class="text-white/40 text-xs mt-0.5">الرصيد المتاح: {{ number_format($walletBalance) }} ج.م</p>
                        </div>
                    </label>

                    {{-- Wallet breakdown (hidden by default, shown when checked) --}}
                    <div id="wallet-breakdown" class="hidden mt-3 bg-green-500/5 border border-green-500/10 rounded-xl p-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-white/60">إجمالي الطلب</span>
                            <span class="text-white font-medium" id="wallet-order-total">{{ number_format($total) }} ج.م</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-400">يُخصم من المحفظة</span>
                            <span class="text-green-400 font-bold" id="wallet-deduct-amount">-{{ number_format(min($walletBalance, $total)) }} ج.م</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-green-500/10" id="wallet-remaining-row">
                            <span class="text-white/60">المتبقي للتحويل</span>
                            <span class="text-brand-red font-bold" id="wallet-remaining-amount">{{ number_format(max(0, $total - $walletBalance)) }} ج.م</span>
                        </div>
                        <p class="text-green-400/60 text-[10px] mt-1" id="wallet-full-cover-msg" style="display:none;">
                            رصيد المحفظة يغطي الطلب بالكامل - لا حاجة لتحويل
                        </p>
                    </div>
                </div>
                @endif

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

                {{-- Submit --}}
                <button type="submit" id="submit-btn"
                    class="w-full bg-brand-red hover:bg-brand-red-dark text-white font-bold py-4 rounded-xl text-lg transition-all shadow-lg shadow-brand-red/20 hover:shadow-brand-red/40 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>تأكيد الدفع وإرسال الطلب</span>
                </button>

                {{-- Order Status Flow --}}
                <div class="flex items-center justify-center gap-2 text-[10px] text-white/30">
                    <span class="bg-white/5 px-2 py-1 rounded">في انتظار التحقق</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span class="bg-white/5 px-2 py-1 rounded">تم التأكيد</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span class="bg-white/5 px-2 py-1 rounded">تم الشحن</span>
                </div>
            </form>
        </div>

        {{-- Order Summary --}}
        <div>
            <div class="bg-brand-dark rounded-2xl p-6 sticky top-20 border border-white/[0.04]">
                <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    ملخص الطلب
                </h2>
                <div class="space-y-3 mb-4">
                    @foreach($items as $item)
                        <div class="flex items-center gap-3 py-1">
                            @if($item['product']->images->first())
                            <div class="w-10 h-10 rounded-lg overflow-hidden bg-white/5 shrink-0">
                                <img src="{{ $item['product']->images->first()->image_url }}" alt="" class="w-full h-full object-cover">
                            </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-white/90 text-xs font-medium line-clamp-1">{{ $item['product']->name_ar ?? $item['product']->name }}</p>
                                <p class="text-white/35 text-[10px]">{{ $item['variant']->size }}/{{ $item['variant']->color }} x {{ $item['quantity'] }}</p>
                            </div>
                            <span class="text-white/70 text-xs font-semibold shrink-0">{{ number_format($item['price'] * $item['quantity']) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-white/[0.06] pt-4 space-y-2.5 text-sm">
                    <div class="flex justify-between text-white/50">
                        <span>المجموع الفرعي</span>
                        <span>{{ number_format($subtotal) }} ج.م</span>
                    </div>
                    <div class="flex justify-between text-white/50">
                        <span>الشحن</span>
                        <span id="shipping-cost-display">@if($shippingDetermined && $shippingCost == 0)<span class="text-green-400 font-medium">مجاني</span>@elseif($shippingCost > 0){{ number_format($shippingCost) }} ج.م @else<span class="text-white/30">يحسب بعد اختيار المحافظة</span>@endif</span>
                    </div>
                    <div id="discount-row" class="hidden flex justify-between text-green-400">
                        <span>الخصم</span>
                        <span id="discount-display">0 ج.م</span>
                    </div>
                    <div class="flex justify-between text-white font-bold text-lg pt-3 border-t border-white/[0.06]">
                        <span>الإجمالي</span>
                        <span class="text-brand-red" id="total-display">{{ number_format($total) }} ج.م</span>
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
// Payment option selection
const paymentOptions = document.querySelectorAll('.payment-option');
paymentOptions.forEach(option => {
    option.querySelector('input[name="payment_type"]').addEventListener('change', function() {
        updatePaymentSelection();
    });
});

function updatePaymentSelection() {
    const selected = document.querySelector('input[name="payment_type"]:checked').value;
    const optionShipping = document.getElementById('option-shipping');
    const optionFull = document.getElementById('option-full');

    // Reset styles
    optionShipping.classList.remove('border-brand-red', 'bg-brand-red/5');
    optionShipping.classList.add('border-white/10');
    optionShipping.querySelector('.option-dot').classList.remove('bg-brand-red');
    optionShipping.querySelector('.option-dot').classList.add('bg-transparent');
    optionShipping.querySelector('.option-dot').parentElement.classList.remove('border-brand-red');
    optionShipping.querySelector('.option-dot').parentElement.classList.add('border-white/30');

    optionFull.classList.remove('border-brand-red', 'bg-brand-red/5', 'border-green-500', 'bg-green-500/5');
    optionFull.classList.add('border-white/10');
    optionFull.querySelector('.option-dot').classList.remove('bg-brand-red', 'bg-green-500');
    optionFull.querySelector('.option-dot').classList.add('bg-transparent');
    optionFull.querySelector('.option-dot').parentElement.classList.remove('border-brand-red', 'border-green-500');
    optionFull.querySelector('.option-dot').parentElement.classList.add('border-white/30');

    if (selected === 'SHIPPING_ONLY') {
        optionShipping.classList.remove('border-white/10');
        optionShipping.classList.add('border-brand-red', 'bg-brand-red/5');
        optionShipping.querySelector('.option-dot').classList.remove('bg-transparent');
        optionShipping.querySelector('.option-dot').classList.add('bg-brand-red');
        optionShipping.querySelector('.option-dot').parentElement.classList.remove('border-white/30');
        optionShipping.querySelector('.option-dot').parentElement.classList.add('border-brand-red');
    } else {
        optionFull.classList.remove('border-white/10');
        optionFull.classList.add('border-green-500', 'bg-green-500/5');
        optionFull.querySelector('.option-dot').classList.remove('bg-transparent');
        optionFull.querySelector('.option-dot').classList.add('bg-green-500');
        optionFull.querySelector('.option-dot').parentElement.classList.remove('border-white/30');
        optionFull.querySelector('.option-dot').parentElement.classList.add('border-green-500');
    }

    updateAmountToPay();
}

function updateAmountToPay() {
    const selected = document.querySelector('input[name="payment_type"]:checked').value;
    const amountEl = document.getElementById('amount-to-pay');
    const shipText = document.getElementById('shipping-cost-display').textContent;
    const shipCost = parseInt(shipText.replace(/[^0-9]/g, '')) || 0;
    const totalText = document.getElementById('total-display').textContent;
    const totalCost = parseInt(totalText.replace(/[^0-9]/g, '')) || 0;

    if (selected === 'SHIPPING_ONLY') {
        if (shipCost > 0) {
            amountEl.textContent = shipCost.toLocaleString() + ' ج.م';
        } else {
            // Free shipping - show deposit amount
            const deposit = calculateDeposit(subtotal - appliedDiscount);
            amountEl.textContent = deposit.toLocaleString() + ' ج.م';
        }
    } else {
        amountEl.textContent = totalCost > 0 ? totalCost.toLocaleString() + ' ج.م' : '---';
    }
}

// Copy InstaPay number
function copyInstapayNumber() {
    const number = document.getElementById('instapay-number').textContent.trim();
    navigator.clipboard.writeText(number).then(() => {
        const btn = document.getElementById('copy-btn');
        const text = document.getElementById('copy-text');
        text.textContent = 'تم النسخ!';
        btn.classList.add('border-green-500/50', 'text-green-400');
        setTimeout(() => {
            text.textContent = 'نسخ رقم InstaPay';
            btn.classList.remove('border-green-500/50', 'text-green-400');
        }, 2000);
    });
}

// File upload handler
function handleFileUpload(input) {
    const file = input.files[0];
    if (!file) return;

    const placeholder = document.getElementById('upload-placeholder');
    const preview = document.getElementById('upload-preview');
    const previewImg = document.getElementById('preview-image');
    const fileName = document.getElementById('file-name');
    const uploadArea = document.getElementById('upload-area');

    if (file.size > 5 * 1024 * 1024) {
        showToast('حجم الملف أكبر من 5MB');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        previewImg.src = e.target.result;
        placeholder.classList.add('hidden');
        preview.classList.remove('hidden');
        fileName.textContent = file.name;
        uploadArea.classList.remove('border-white/20');
        uploadArea.classList.add('border-green-500/50');
    };
    reader.readAsDataURL(file);
}

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
const depositMin = {{ (float) \App\Models\SiteSetting::get('deposit_min', 100) }};
const depositPercentage = {{ (float) \App\Models\SiteSetting::get('deposit_percentage', 10) }};
const freeShippingThreshold = {{ (float) \App\Models\SiteSetting::get('free_shipping_threshold', 2000) }};

function calculateDeposit(orderSubtotal) {
    const percentageAmount = Math.ceil(orderSubtotal * (depositPercentage / 100));
    return Math.max(depositMin, percentageAmount);
}

function updatePartialOption(shippingCost) {
    const titleEl = document.getElementById('partial-title');
    const descEl = document.getElementById('partial-desc');
    const labelEl = document.getElementById('partial-fee-label');
    const amountEl = document.getElementById('shipping-pay-amount');
    const remainEl = document.getElementById('remaining-amount');
    const depositInput = document.getElementById('deposit-amount-input');
    const freeMsg = document.getElementById('free-shipping-msg');
    const shipDisplay = document.getElementById('shipping-cost-display');
    const currentSubtotal = subtotal - appliedDiscount;

    if (shippingCost > 0) {
        titleEl.textContent = 'ادفع الشحن فقط';
        descEl.textContent = 'ادفع رسوم الشحن الآن لتأكيد طلبك. المبلغ المتبقي يُدفع عند الاستلام.';
        labelEl.textContent = 'رسوم الشحن';
        amountEl.textContent = shippingCost.toLocaleString() + ' ج.م';
        remainEl.textContent = currentSubtotal.toLocaleString() + ' ج.م';
        depositInput.value = shippingCost;
        if (freeMsg) freeMsg.classList.add('hidden');
        if (shipDisplay) shipDisplay.innerHTML = shippingCost.toLocaleString() + ' ج.م';
    } else {
        const deposit = calculateDeposit(currentSubtotal);
        titleEl.textContent = 'ادفع عربون تأكيد';
        descEl.textContent = 'ادفع عربون بسيط لتأكيد جدية طلبك. المبلغ المتبقي يُدفع عند الاستلام.';
        labelEl.textContent = 'عربون التأكيد';
        amountEl.textContent = deposit.toLocaleString() + ' ج.م';
        remainEl.textContent = (currentSubtotal - deposit).toLocaleString() + ' ج.م';
        depositInput.value = deposit;
        if (freeMsg) freeMsg.classList.remove('hidden');
        if (shipDisplay) shipDisplay.innerHTML = '<span class="text-green-400">مجاني</span>';
    }
    updateAmountToPay();
}

function fetchShippingCost() {
    const gov = govSelect ? govSelect.value : '';
    const city = citySelect ? citySelect.value : '';
    if (!gov) return;

    fetch(`{{ route('api.shipping.cost') }}?governorate=${encodeURIComponent(gov)}&city=${encodeURIComponent(city)}`)
        .then(r => r.json())
        .then(data => {
            const cost = data.cost;
            const total = subtotal - appliedDiscount + cost;
            const shipEl = document.getElementById('shipping-cost-display');
            const totalEl = document.getElementById('total-display');
            const fullPayEl = document.getElementById('full-pay-amount');

            if (shipEl) shipEl.textContent = cost > 0 ? cost.toLocaleString() + ' ج.م' : 'مجاني';
            if (totalEl) totalEl.textContent = total.toLocaleString() + ' ج.م';
            if (fullPayEl) fullPayEl.textContent = total.toLocaleString() + ' ج.م';

            updatePartialOption(cost);
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
            var fullPayEl = document.getElementById('full-pay-amount');
            var cost = data.cost;
            var total = subtotal - appliedDiscount + cost;

            if (shipEl) shipEl.textContent = cost > 0 ? cost.toLocaleString() + ' ج.م' : 'مجاني';
            if (totalEl) totalEl.textContent = total.toLocaleString() + ' ج.م';
            if (fullPayEl) fullPayEl.textContent = total.toLocaleString() + ' ج.م';

            updatePartialOption(cost);
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
            var list = document.getElementById('addresses-list');
            var noMsg = document.getElementById('no-addresses-msg');
            if (noMsg) noMsg.remove();

            var container = list.querySelector('.space-y-2');
            if (!container) {
                container = document.createElement('div');
                container.className = 'space-y-2';
                list.appendChild(container);
            }

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

            document.getElementById('new-address-panel').classList.add('hidden');
            fetchShippingForAddress(data.governorate, data.city);
            showToast('تم إضافة العنوان بنجاح');

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

    const fullPayEl = document.getElementById('full-pay-amount');
    if (fullPayEl) fullPayEl.textContent = total.toLocaleString() + ' ج.م';

    updatePartialOption(shipCost);

    // Refresh wallet breakdown if active
    var cb = document.getElementById('use-wallet-check');
    if (cb && cb.checked) updateWalletBreakdown();
}

// Wallet
const walletBalance = {{ $walletBalance ?? 0 }};

function toggleWallet() {
    var cb = document.getElementById('use-wallet-check');
    var input = document.getElementById('use-wallet-input');
    var breakdown = document.getElementById('wallet-breakdown');
    var label = document.getElementById('wallet-toggle-label');
    var proofInput = document.getElementById('payment-proof-input');

    if (!cb || !input) return;

    if (cb.checked) {
        input.value = walletBalance;
        if (breakdown) breakdown.classList.remove('hidden');
        if (label) { label.style.borderColor = 'rgba(34,197,94,0.4)'; label.style.background = 'rgba(34,197,94,0.05)'; }
        updateWalletBreakdown();
    } else {
        input.value = 0;
        if (breakdown) breakdown.classList.add('hidden');
        if (label) { label.style.borderColor = 'rgba(255,255,255,0.1)'; label.style.background = 'transparent'; }
        if (proofInput) proofInput.required = true;
    }
}

function updateWalletBreakdown() {
    var totalText = document.getElementById('total-display').textContent;
    var orderTotal = parseInt(totalText.replace(/[^0-9]/g, '')) || 0;
    var walletDeduct = Math.min(walletBalance, orderTotal);
    var remaining = Math.max(0, orderTotal - walletBalance);

    var deductEl = document.getElementById('wallet-deduct-amount');
    var remainEl = document.getElementById('wallet-remaining-amount');
    var orderTotalEl = document.getElementById('wallet-order-total');
    var remainRow = document.getElementById('wallet-remaining-row');
    var fullMsg = document.getElementById('wallet-full-cover-msg');
    var proofInput = document.getElementById('payment-proof-input');
    var instapaySection = document.getElementById('instapay-section');

    if (deductEl) deductEl.textContent = '-' + walletDeduct.toLocaleString() + ' ج.م';
    if (remainEl) remainEl.textContent = remaining.toLocaleString() + ' ج.م';
    if (orderTotalEl) orderTotalEl.textContent = orderTotal.toLocaleString() + ' ج.م';

    if (remaining <= 0) {
        // Wallet covers everything
        if (remainRow) remainRow.style.display = 'none';
        if (fullMsg) fullMsg.style.display = 'block';
        if (proofInput) proofInput.required = false;
        if (instapaySection) instapaySection.style.display = 'none';
    } else {
        if (remainRow) remainRow.style.display = 'flex';
        if (fullMsg) fullMsg.style.display = 'none';
        if (proofInput) proofInput.required = true;
        if (instapaySection) instapaySection.style.display = 'block';
    }
}

// Initialize payment selection
updatePaymentSelection();

// Auto-fetch shipping for logged-in users with pre-selected address
@if(!$isGuest && $addresses->count())
    @php $defaultAddr = $addresses->first(); @endphp
    fetchShippingForAddress('{{ $defaultAddr->governorate }}', '{{ $defaultAddr->city }}');
@endif
</script>
@endpush
