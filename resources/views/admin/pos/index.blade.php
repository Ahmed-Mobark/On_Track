@extends('layouts.admin')
@section('title', 'نقطة البيع')

@section('content')
<h1 class="text-2xl font-bold text-white mb-6">نقطة البيع (POS)</h1>

@if(!$activeSession)
    {{-- Open Session --}}
    <div class="max-w-md mx-auto bg-brand-dark rounded-xl p-8 text-center">
        <h2 class="text-white text-lg font-bold mb-4">فتح وردية جديدة</h2>
        <form action="{{ route('admin.pos.session.open') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-white/70 text-sm mb-1">المبلغ الافتتاحي</label>
                <input type="number" name="opening_cash" required step="0.01" min="0"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white text-center text-lg focus:outline-none focus:border-brand-red" dir="ltr">
            </div>
            <button type="submit" class="w-full bg-brand-red hover:bg-brand-red-dark text-white py-3 rounded-xl font-semibold transition-colors">
                فتح الوردية
            </button>
        </form>
    </div>
@else
    {{-- POS Interface --}}
    <div class="grid lg:grid-cols-3 gap-6" id="pos-app">
        {{-- Product Search --}}
        <div class="lg:col-span-2">
            <div class="bg-brand-dark rounded-xl p-4 mb-4">
                <input type="text" id="pos-search" placeholder="بحث بالاسم أو الباركود..."
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red">
            </div>
            <div id="pos-products" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                {{-- Products loaded via JS --}}
            </div>
        </div>

        {{-- Cart & Checkout --}}
        <div class="bg-brand-dark rounded-xl p-4 h-fit sticky top-20">
            <h3 class="text-white font-bold mb-3">الفاتورة</h3>
            <div id="pos-cart" class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                <p class="text-white/40 text-sm text-center py-4">لا توجد منتجات</p>
            </div>
            <div class="border-t border-white/10 pt-3 space-y-2 text-sm">
                <div class="flex justify-between text-white/60"><span>المجموع</span><span id="pos-subtotal">0</span></div>
                <div class="flex justify-between text-white font-bold text-lg"><span>الإجمالي</span><span id="pos-total">0 ج.م</span></div>
            </div>
            <div class="mt-4 space-y-2">
                <select id="pos-payment" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm">
                    <option value="CASH">كاش</option>
                    <option value="VISA">فيزا</option>
                    <option value="INSTAPAY">انستاباي</option>
                    <option value="WALLET">محفظة</option>
                </select>
                <input type="number" id="pos-amount-paid" placeholder="المبلغ المدفوع" step="0.01"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm placeholder:text-white/30 focus:outline-none" dir="ltr">
                <button onclick="submitPosTransaction()" class="w-full bg-brand-red hover:bg-brand-red-dark text-white py-3 rounded-xl font-semibold transition-colors">
                    إتمام البيع
                </button>
            </div>

            {{-- Close Session --}}
            <div class="mt-6 pt-4 border-t border-white/10">
                <details>
                    <summary class="text-white/40 text-xs cursor-pointer">إغلاق الوردية</summary>
                    <form action="{{ route('admin.pos.session.close') }}" method="POST" class="mt-3 space-y-2">
                        @csrf
                        <input type="number" name="closing_cash" required step="0.01" placeholder="المبلغ النهائي"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm placeholder:text-white/30" dir="ltr">
                        <button type="submit" class="w-full bg-white/10 hover:bg-white/20 text-white py-2 rounded-lg text-sm transition-colors">إغلاق</button>
                    </form>
                </details>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let cart = [];
        const sessionId = '{{ $activeSession->id }}';

        document.getElementById('pos-search').addEventListener('input', debounce(searchProducts, 300));

        async function searchProducts(e) {
            const q = e.target.value;
            if (q.length < 2) return;
            const res = await fetch(`{{ route('admin.pos.search') }}?q=${encodeURIComponent(q)}`);
            const products = await res.json();
            renderProducts(products);
        }

        function renderProducts(products) {
            const container = document.getElementById('pos-products');
            container.innerHTML = products.map(p => `
                <div class="bg-white/5 rounded-lg p-3 cursor-pointer hover:bg-white/10 transition-colors" onclick='showVariants(${JSON.stringify(p)})'>
                    <div class="aspect-square bg-white/5 rounded-lg mb-2 overflow-hidden">
                        ${p.images?.[0] ? `<img src="${p.images[0].url}" class="w-full h-full object-cover">` : ''}
                    </div>
                    <p class="text-white text-xs font-medium line-clamp-1">${p.name_ar || p.name}</p>
                    <p class="text-brand-red text-xs font-bold">${p.base_price} ج.م</p>
                </div>
            `).join('');
        }

        function showVariants(product) {
            if (product.variants.length === 1) {
                addToCart(product, product.variants[0]);
            } else if (product.variants.length > 0) {
                const variant = product.variants[0]; // Simple: pick first
                addToCart(product, variant);
            }
        }

        function addToCart(product, variant) {
            const existing = cart.find(i => i.variant_id === variant.id);
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({
                    product_id: product.id,
                    variant_id: variant.id,
                    name: product.name_ar || product.name,
                    size: variant.size,
                    color: variant.color,
                    price: parseFloat(variant.price || product.base_price),
                    quantity: 1,
                });
            }
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('pos-cart');
            if (!cart.length) {
                container.innerHTML = '<p class="text-white/40 text-sm text-center py-4">لا توجد منتجات</p>';
                document.getElementById('pos-subtotal').textContent = '0';
                document.getElementById('pos-total').textContent = '0 ج.م';
                return;
            }
            container.innerHTML = cart.map((item, i) => `
                <div class="flex items-center gap-2 py-2 border-b border-white/5">
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-xs line-clamp-1">${item.name}</p>
                        <p class="text-white/40 text-[10px]">${item.size}/${item.color}</p>
                    </div>
                    <input type="number" value="${item.quantity}" min="1" max="99"
                        class="w-12 bg-white/5 border border-white/10 rounded px-1 py-0.5 text-white text-center text-xs"
                        onchange="updateQty(${i}, this.value)">
                    <span class="text-white text-xs font-medium w-16 text-left">${(item.price * item.quantity).toFixed(0)}</span>
                    <button onclick="removeFromCart(${i})" class="text-red-400 text-xs">✕</button>
                </div>
            `).join('');

            const subtotal = cart.reduce((s, i) => s + i.price * i.quantity, 0);
            document.getElementById('pos-subtotal').textContent = subtotal.toFixed(0) + ' ج.م';
            document.getElementById('pos-total').textContent = subtotal.toFixed(0) + ' ج.م';
        }

        function updateQty(index, qty) { cart[index].quantity = parseInt(qty) || 1; renderCart(); }
        function removeFromCart(index) { cart.splice(index, 1); renderCart(); }

        async function submitPosTransaction() {
            if (!cart.length) return alert('السلة فارغة');
            const subtotal = cart.reduce((s, i) => s + i.price * i.quantity, 0);
            const amountPaid = parseFloat(document.getElementById('pos-amount-paid').value) || subtotal;

            const res = await fetch('{{ route("admin.pos.transaction") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    items: cart.map(i => ({ product_id: i.product_id, variant_id: i.variant_id, quantity: i.quantity, price: i.price })),
                    payment_method: document.getElementById('pos-payment').value,
                    amount_paid: amountPaid,
                })
            });

            if (res.ok) {
                const data = await res.json();
                alert(`تم البيع بنجاح! رقم: ${data.transaction_number}\nالباقي: ${data.change_amount} ج.م`);
                cart = [];
                renderCart();
            } else {
                const err = await res.json();
                alert(err.message || 'حدث خطأ');
            }
        }

        function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    </script>
    @endpush
@endif
@endsection
