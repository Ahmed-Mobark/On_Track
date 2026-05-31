<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiAssistantController extends Controller
{
    public function query(Request $request)
    {
        $request->validate(['question' => 'required|string|max:500']);

        $question = mb_strtolower($request->question);
        $result = $this->processQuestion($question);

        return response()->json($result);
    }

    private function processQuestion(string $question): array
    {
        // Normalize: remove tashkeel, extra spaces
        $q = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $question);
        $q = preg_replace('/\s+/', ' ', trim($q));

        // Search for specific order number first
        if (preg_match('/(OT-[\w-]+)/i', $q, $matches)) {
            return $this->findOrder($matches[1]);
        }

        // Compound queries: "حالة دفع اخر اوردر" / "تفاصيل اخر طلب" / "دفع اخر اوردر"
        $isLastOrder = $this->matches($q, ['اخر اوردر', 'آخر اوردر', 'اخر طلب', 'آخر طلب', 'last order', 'اخر order', 'اخر اردر']);
        $wantsPayment = $this->matches($q, ['دفع', 'حاله دفع', 'حالة دفع', 'payment', 'مدفوع', 'دفع كام', 'باقي كام']);

        if ($isLastOrder && $wantsPayment) {
            return $this->lastOrderPayment();
        }
        if ($isLastOrder) {
            return $this->lastOrder();
        }

        // Define intent patterns
        $intents = [
            'pendingOrders' => ['معلق', 'pending', 'في الانتظار', 'لسه معلق', 'لسة معلق', 'مستني', 'طلبات معلقه', 'اوردرات معلقه', 'المعلقه', 'المعلقة'],
            'todayOrders' => ['النهارد', 'النهاردة', 'اليوم', 'today', 'انهارده', 'النهرده', 'طلبات اليوم'],
            'weekOrders' => ['الاسبوع', 'الأسبوع', 'this week', 'اسبوع', 'هذا الاسبوع'],
            'monthOrders' => ['الشهر', 'this month', 'شهر', 'هذا الشهر', 'الشهر ده'],
            'revenue' => ['ايراد', 'إيراد', 'ارباح', 'أرباح', 'revenue', 'مبيعات', 'فلوس', 'كام فلوس', 'كسبنا', 'دخل', 'اجمالي المبيعات', 'ايرادات', 'كام بعنا', 'حصلنا كام'],
            'pendingPayments' => ['تحقق', 'تاكيد دفع', 'تأكيد دفع', 'لسه مأكدتش', 'ماكدتش', 'payment pending', 'اثبات دفع', 'دفع معلق', 'لسه مدفعش', 'مستني تاكيد', 'سكرين', 'اثبات', 'حاله دفع', 'حالة دفع'],
            'cancelledOrders' => ['ملغ', 'cancelled', 'مرتجع', 'returned', 'الغاء', 'كانسل', 'cancel', 'رجع', 'مرجع', 'ملغي', 'ملغيه'],

            'bestSellingProducts' => ['اكتر منتج', 'أكتر منتج', 'best sell', 'الاكثر مبيع', 'top product', 'اكتر حاجه اتباعت', 'ايه اكتر حاجه', 'منتج مبيع', 'اكتر مبيعا'],
            'outOfStock' => ['نفذ', 'نفد', 'out of stock', 'خلص', 'خلس', 'مخزون صفر', 'خلصان', 'خلصانه', 'مفيش مخزون', 'نفذت'],
            'lowStock' => ['مخزون قليل', 'low stock', 'قرب يخلص', 'هيخلص', 'مخزون ضعيف', 'باقي قليل', 'وشك يخلص'],
            'productCount' => ['كام منتج', 'عدد المنتجات', 'total products', 'كم منتج', 'منتجات كام'],

            'customerCount' => ['كام عميل', 'عدد العملاء', 'total customers', 'عدد الزباين', 'كم عميل', 'العملاء كام', 'زباين', 'كام زبون'],
            'topCustomers' => ['اكتر عميل', 'أكتر عميل', 'top customer', 'افضل عميل', 'أفضل عميل', 'اكبر عميل', 'أكبر عميل', 'اكتر زبون', 'اكتر حد اشتري', 'مين اشتري اكتر'],
            'newCustomers' => ['عملاء جدد', 'new customers', 'عملاء جداد', 'زباين جدد', 'مسجلين جدد'],

            'activeCoupons' => ['كوبون', 'coupon', 'كود خصم', 'كوبونات', 'اكواد', 'كوبون فعال'],
            'topGovernorates' => ['محافظ', 'اكتر محافظ', 'أكتر محافظه', 'محافظات', 'governorate', 'مناطق', 'اكتر منطقه'],
            'pointsStats' => ['نقاط', 'points', 'محفظ', 'wallet', 'رصيد', 'ولاء', 'loyalty'],
            'overview' => ['ملخص', 'summary', 'احصائيات', 'إحصائيات', 'overview', 'نظره عامه', 'ازيك', 'أزيك', 'اخبار', 'أخبار', 'ايه الاخبار', 'عامل ايه', 'ايه الجديد', 'احوال', 'stats', 'ارقام', 'dashboard'],
        ];

        foreach ($intents as $method => $keywords) {
            if ($this->matches($q, $keywords)) {
                return $this->$method();
            }
        }

        // Search for customer by name/phone
        if ($this->matches($q, ['ابحث', 'بحث', 'دور', 'search', 'فين', 'عميل', 'زبون'])) {
            $searchTerm = preg_replace('/(ابحث|بحث|دور|search|فين|عن|عميل|زبون|اسمه|اسمها|بيانات|رقم)/u', '', $q);
            $searchTerm = trim($searchTerm);
            if (mb_strlen($searchTerm) >= 2) {
                return $this->findCustomer($searchTerm);
            }
        }

        // Fuzzy match: single word
        $words = explode(' ', $q);
        foreach ($intents as $method => $keywords) {
            foreach ($words as $word) {
                if (mb_strlen($word) < 3) continue;
                foreach ($keywords as $keyword) {
                    if (mb_strlen($keyword) >= 3 && (mb_strpos($keyword, $word) !== false || mb_strpos($word, $keyword) !== false)) {
                        return $this->$method();
                    }
                }
            }
        }

        return ['answer' => "مش فاهم السؤال ده. ممكن تجرب تسألني:\n\n"
            . "📦 الطلبات: آخر طلب، المعلقة، طلبات النهاردة، الأسبوع، الشهر\n"
            . "💰 الفلوس: إيرادات، مبيعات، حالة دفع آخر أوردر\n"
            . "📊 المنتجات: أكتر منتج مبيعاً، نفذ من المخزون، مخزون قليل\n"
            . "👥 العملاء: أكتر عميل، عملاء جدد، ابحث عن [اسم]\n"
            . "🏷️ تاني: كوبونات، محافظات، ملخص عام\n"
            . "🔍 ابحث عن أوردر: OT-xxxxx"];
    }

    private function matches(string $question, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (mb_strpos($question, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    // ========== Helpers ==========

    private function r(string $answer, array $actions = []): array
    {
        return array_filter(['answer' => $answer, 'actions' => $actions ?: null]);
    }

    private function orderLink(Order $order): array
    {
        return ['label' => "فتح #{$order->order_number}", 'url' => route('admin.orders.show', $order)];
    }

    // ========== Order Queries ==========

    private function lastOrder(): array
    {
        $order = Order::with(['user', 'items.product'])->latest()->first();
        if (!$order) return $this->r('لا يوجد طلبات بعد.');

        $customer = $order->user ? ($order->user->first_name . ' ' . $order->user->last_name) : 'زائر';
        $items = $order->items->map(fn($i) => ($i->product->name_ar ?? $i->product->name) . ' x' . $i->quantity)->implode(', ');

        $text = "آخر طلب: #{$order->order_number}\n"
            . "العميل: {$customer}\n"
            . "المنتجات: {$items}\n"
            . "الإجمالي: " . number_format($order->total) . " ج.م\n"
            . "الحالة: " . $this->statusAr($order->status) . "\n"
            . "الدفع: " . $this->paymentStatusAr($order->payment_status) . "\n"
            . "التاريخ: " . $order->created_at->format('Y/m/d H:i');

        $actions = [$this->orderLink($order)];
        if ($order->payment_proof && $order->payment_status === 'PENDING') {
            $actions[] = ['label' => 'تأكيد الدفع', 'url' => route('admin.orders.show', $order), 'style' => 'green'];
        }

        return $this->r($text, $actions);
    }

    private function lastOrderPayment(): array
    {
        $order = Order::with('user')->latest()->first();
        if (!$order) return $this->r('لا يوجد طلبات بعد.');

        $customer = $order->user ? ($order->user->first_name . ' ' . $order->user->last_name) : 'زائر';
        $depositAmount = $order->deposit_amount ? number_format($order->deposit_amount) : '0';
        $remaining = $order->deposit_amount ? number_format($order->total - $order->deposit_amount) : number_format($order->total);
        $payType = $order->payment_type === 'SHIPPING_ONLY' ? ($order->shipping_cost > 0 ? 'شحن فقط' : 'عربون تأكيد') : 'دفع كامل';

        $text = "حالة دفع آخر طلب #{$order->order_number}\n"
            . "العميل: {$customer}\n"
            . "━━━━━━━━━━━━━━\n"
            . "نوع الدفع: {$payType}\n"
            . "المبلغ المدفوع: {$depositAmount} ج.م\n"
            . "المتبقي عند الاستلام: {$remaining} ج.م\n"
            . "الإجمالي: " . number_format($order->total) . " ج.م\n"
            . "━━━━━━━━━━━━━━\n"
            . "حالة الدفع: " . $this->paymentStatusAr($order->payment_status) . "\n"
            . "إثبات دفع: " . ($order->payment_proof ? 'مرفق' : 'لا يوجد');

        $actions = [$this->orderLink($order)];
        if ($order->payment_proof) {
            $actions[] = ['label' => 'عرض إثبات الدفع', 'url' => asset('storage/' . $order->payment_proof), 'style' => 'blue', 'target' => '_blank'];
        }
        if ($order->payment_proof && $order->payment_status === 'PENDING') {
            $actions[] = ['label' => 'تأكيد الدفع', 'url' => route('admin.orders.show', $order), 'style' => 'green'];
        }

        return $this->r($text, $actions);
    }

    private function pendingOrders(): array
    {
        $orders = Order::where('status', 'PENDING')->with('user')->latest()->take(10)->get();
        if ($orders->isEmpty()) return $this->r('لا يوجد طلبات معلقة. كل الطلبات تم معالجتها!');

        $total = Order::where('status', 'PENDING')->count();
        $lines = $orders->map(function ($o) {
            $name = $o->user ? $o->user->first_name : 'زائر';
            return "- #{$o->order_number} | {$name} | " . number_format($o->total) . " ج.م | " . $o->created_at->diffForHumans();
        })->implode("\n");

        $actions = $orders->take(3)->map(fn($o) => $this->orderLink($o))->toArray();

        return $this->r("الطلبات المعلقة ({$total} طلب):\n{$lines}", $actions);
    }

    private function todayOrders(): array
    {
        $today = now()->startOfDay();
        $orders = Order::where('created_at', '>=', $today)->get();
        return $this->r("طلبات اليوم:\nالعدد: {$orders->count()} طلب\nالإجمالي: " . number_format($orders->sum('total')) . " ج.م\nمؤكد: {$orders->where('status','CONFIRMED')->count()} | معلق: {$orders->where('status','PENDING')->count()}");
    }

    private function weekOrders(): array
    {
        $orders = Order::where('created_at', '>=', now()->startOfWeek())->get();
        $count = $orders->count();
        $total = $orders->sum('total');
        return $this->r("طلبات الأسبوع:\nالعدد: {$count} طلب\nالإجمالي: " . number_format($total) . " ج.م\nالمتوسط: " . ($count > 0 ? number_format($total / $count) : 0) . " ج.م للطلب");
    }

    private function monthOrders(): array
    {
        $orders = Order::where('created_at', '>=', now()->startOfMonth())->get();
        return $this->r("طلبات الشهر:\nالعدد: {$orders->count()} طلب\nالإجمالي: " . number_format($orders->sum('total')) . " ج.م\nالمحصّل: " . number_format($orders->where('payment_status', 'PAID')->sum('total')) . " ج.م");
    }

    private function revenue(): array
    {
        $confirmed = ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED'];
        return $this->r("إجمالي الإيرادات:\nإجمالي كلي: " . number_format(Order::whereIn('status', $confirmed)->sum('total')) . " ج.م\nالمحصّل فعلياً: " . number_format(Order::where('payment_status', 'PAID')->sum('total')) . " ج.م\nهذا الشهر: " . number_format(Order::where('created_at', '>=', now()->startOfMonth())->whereIn('status', $confirmed)->sum('total')) . " ج.م\nهذا الأسبوع: " . number_format(Order::where('created_at', '>=', now()->startOfWeek())->whereIn('status', $confirmed)->sum('total')) . " ج.م");
    }

    private function pendingPayments(): array
    {
        $orders = Order::where('payment_status', 'PENDING')->whereNotNull('payment_proof')->with('user')->latest()->take(10)->get();
        if ($orders->isEmpty()) return $this->r('لا يوجد طلبات في انتظار التحقق من الدفع.');

        $total = Order::where('payment_status', 'PENDING')->whereNotNull('payment_proof')->count();
        $lines = $orders->map(function ($o) {
            $name = $o->user ? $o->user->first_name : 'زائر';
            $type = $o->payment_type === 'SHIPPING_ONLY' ? 'جزئي' : 'كامل';
            return "- #{$o->order_number} | {$name} | " . number_format($o->deposit_amount ?? 0) . " ج.م ({$type})";
        })->implode("\n");

        $actions = $orders->take(3)->map(fn($o) => $this->orderLink($o))->toArray();
        return $this->r("طلبات في انتظار تأكيد الدفع ({$total}):\n{$lines}", $actions);
    }

    private function cancelledOrders(): array
    {
        $cancelled = Order::where('status', 'CANCELLED')->count();
        $returned = Order::where('status', 'RETURNED')->count();
        $thisMonth = Order::whereIn('status', ['CANCELLED', 'RETURNED'])->where('created_at', '>=', now()->startOfMonth())->count();
        $totalOrders = Order::count();
        $rate = $totalOrders > 0 ? round(($cancelled + $returned) / $totalOrders * 100, 1) : 0;
        return $this->r("الطلبات الملغية والمرتجعة:\nملغي: {$cancelled}\nمرتجع: {$returned}\nهذا الشهر: {$thisMonth}\nنسبة الإلغاء/الإرجاع: {$rate}%");
    }

    private function findOrder(string $orderNumber): array
    {
        $order = Order::where('order_number', 'LIKE', "%{$orderNumber}%")->with(['user', 'items.product', 'address'])->first();
        if (!$order) return $this->r("مش لاقي أوردر برقم {$orderNumber}");

        $customer = $order->user ? ($order->user->first_name . ' ' . $order->user->last_name) : 'زائر';
        $items = $order->items->map(fn($i) => ($i->product->name_ar ?? $i->product->name) . ' x' . $i->quantity)->implode(', ');
        $address = $order->address ? "{$order->address->city}, {$order->address->governorate}" : '-';
        $depositInfo = $order->deposit_amount ? "\nالمدفوع مقدماً: " . number_format($order->deposit_amount) . " ج.م\nالمتبقي: " . number_format($order->total - $order->deposit_amount) . " ج.م" : '';

        $text = "طلب #{$order->order_number}\nالعميل: {$customer}\nالعنوان: {$address}\nالمنتجات: {$items}\nالإجمالي: " . number_format($order->total) . " ج.م{$depositInfo}\nالحالة: " . $this->statusAr($order->status) . "\nالدفع: " . $this->paymentStatusAr($order->payment_status) . "\nالتاريخ: " . $order->created_at->format('Y/m/d H:i');

        $actions = [$this->orderLink($order)];
        if ($order->payment_proof) {
            $actions[] = ['label' => 'إثبات الدفع', 'url' => asset('storage/' . $order->payment_proof), 'style' => 'blue', 'target' => '_blank'];
        }
        if ($order->payment_proof && $order->payment_status === 'PENDING') {
            $actions[] = ['label' => 'تأكيد الدفع', 'url' => route('admin.orders.show', $order), 'style' => 'green'];
        }
        return $this->r($text, $actions);
    }

    // ========== Product Queries ==========

    private function bestSellingProducts(): array
    {
        $products = DB::table('order_items')->select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(price * quantity) as total_revenue'))->groupBy('product_id')->orderByDesc('total_sold')->take(5)->get();
        if ($products->isEmpty()) return $this->r('لا يوجد مبيعات بعد.');
        $lines = $products->map(function ($p) { $product = Product::find($p->product_id); $name = $product ? ($product->name_ar ?? $product->name) : 'محذوف'; return "- {$name} | بيع {$p->total_sold} قطعة | " . number_format($p->total_revenue) . " ج.م"; })->implode("\n");
        return $this->r("أكثر المنتجات مبيعاً:\n{$lines}");
    }

    private function outOfStock(): array
    {
        $variants = ProductVariant::where('quantity', '<=', 0)->with('product:id,name,name_ar')->get();
        if ($variants->isEmpty()) return $this->r('كل المنتجات متوفرة في المخزون!');
        $lines = $variants->take(15)->map(fn($v) => "- " . ($v->product ? ($v->product->name_ar ?? $v->product->name) : '?') . " ({$v->size}/{$v->color})")->implode("\n");
        return $this->r("منتجات نفذت من المخزون ({$variants->count()}):\n{$lines}", [['label' => 'إدارة المخزون', 'url' => route('admin.inventory.index')]]);
    }

    private function lowStock(): array
    {
        $variants = ProductVariant::where('quantity', '>', 0)->where('quantity', '<=', 5)->with('product:id,name,name_ar')->orderBy('quantity')->get();
        if ($variants->isEmpty()) return $this->r('لا يوجد منتجات بمخزون قليل.');
        $lines = $variants->take(15)->map(fn($v) => "- " . ($v->product ? ($v->product->name_ar ?? $v->product->name) : '?') . " ({$v->size}/{$v->color}) - متبقي {$v->quantity}")->implode("\n");
        return $this->r("منتجات بمخزون قليل ({$variants->count()}):\n{$lines}", [['label' => 'إدارة المخزون', 'url' => route('admin.inventory.index')]]);
    }

    private function productCount(): array
    {
        return $this->r("المنتجات:\nإجمالي: " . Product::count() . "\nنشط: " . Product::where('is_active', true)->count() . "\nإجمالي الفاريانتات: " . ProductVariant::count(), [['label' => 'إدارة المنتجات', 'url' => route('admin.products.index')]]);
    }

    // ========== Customer Queries ==========

    private function customerCount(): array
    {
        return $this->r("العملاء:\nإجمالي: " . User::where('role', 'CUSTOMER')->count() . "\nجدد هذا الشهر: " . User::where('role', 'CUSTOMER')->where('created_at', '>=', now()->startOfMonth())->count() . "\nعملاء لديهم طلبات: " . User::where('role', 'CUSTOMER')->whereHas('orders')->count());
    }

    private function topCustomers(): array
    {
        $customers = DB::table('orders')->select('user_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total) as total_spent'))->groupBy('user_id')->orderByDesc('total_spent')->take(5)->get();
        $lines = $customers->map(function ($c) { $user = User::find($c->user_id); $name = $user ? ($user->first_name . ' ' . $user->last_name) : 'محذوف'; return "- {$name} | {$c->order_count} طلب | " . number_format($c->total_spent) . " ج.م"; })->implode("\n");
        return $this->r("أكثر العملاء شراءً:\n{$lines}");
    }

    private function newCustomers(): array
    {
        return $this->r("العملاء الجدد:\nاليوم: " . User::where('role', 'CUSTOMER')->where('created_at', '>=', now()->startOfDay())->count() . "\nالأسبوع: " . User::where('role', 'CUSTOMER')->where('created_at', '>=', now()->startOfWeek())->count() . "\nالشهر: " . User::where('role', 'CUSTOMER')->where('created_at', '>=', now()->startOfMonth())->count());
    }

    private function findCustomer(string $name): array
    {
        $users = User::where('role', 'CUSTOMER')->where(fn($q) => $q->where('first_name', 'LIKE', "%{$name}%")->orWhere('last_name', 'LIKE', "%{$name}%")->orWhere('email', 'LIKE', "%{$name}%")->orWhere('phone', 'LIKE', "%{$name}%"))->take(5)->get();
        if ($users->isEmpty()) return $this->r("مش لاقي عميل باسم \"{$name}\"");
        $lines = $users->map(function ($u) { $orders = $u->orders()->count(); $spent = $u->orders()->sum('total'); return "- {$u->first_name} {$u->last_name} | {$u->phone} | {$orders} طلب | " . number_format($spent) . " ج.م"; })->implode("\n");
        return $this->r("نتائج البحث:\n{$lines}");
    }

    // ========== Other Queries ==========

    private function activeCoupons(): array
    {
        $coupons = Coupon::where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->get();
        if ($coupons->isEmpty()) return $this->r('لا يوجد كوبونات فعالة.');
        $lines = $coupons->map(function ($c) { $type = $c->type === 'PERCENTAGE' ? $c->value . '%' : number_format($c->value) . ' ج.م'; return "- {$c->code} | خصم {$type} | استخدام " . $c->used_count . ($c->max_uses ? '/' . $c->max_uses : ''); })->implode("\n");
        return $this->r("الكوبونات الفعالة ({$coupons->count()}):\n{$lines}");
    }

    private function topGovernorates(): array
    {
        $govs = DB::table('orders')->join('addresses', 'orders.address_id', '=', 'addresses.id')->select('addresses.governorate', DB::raw('COUNT(*) as order_count'))->groupBy('addresses.governorate')->orderByDesc('order_count')->take(10)->get();
        if ($govs->isEmpty()) return $this->r('لا يوجد بيانات كافية.');
        $lines = $govs->map(fn($g) => "- {$g->governorate}: {$g->order_count} طلب")->implode("\n");
        return $this->r("أكثر المحافظات طلبات:\n{$lines}");
    }

    private function overview(): array
    {
        $pending = Order::where('status', 'PENDING')->count();
        $pendingPayments = Order::where('payment_status', 'PENDING')->whereNotNull('payment_proof')->count();
        $text = "ملخص عام:\nإجمالي الطلبات: " . Order::count() . "\nطلبات معلقة: {$pending}\nفي انتظار تأكيد الدفع: {$pendingPayments}\nطلبات اليوم: " . Order::where('created_at', '>=', now()->startOfDay())->count() . "\nإيراد اليوم: " . number_format(Order::where('created_at', '>=', now()->startOfDay())->sum('total')) . " ج.م\nإيراد الشهر: " . number_format(Order::where('created_at', '>=', now()->startOfMonth())->sum('total')) . " ج.م\nمنتجات نفذت: " . ProductVariant::where('quantity', '<=', 0)->count() . "\nعدد العملاء: " . User::where('role', 'CUSTOMER')->count();

        $actions = [];
        if ($pending > 0) $actions[] = ['label' => "المعلقة ({$pending})", 'url' => route('admin.orders.index') . '?status=PENDING'];
        if ($pendingPayments > 0) $actions[] = ['label' => "تأكيد دفع ({$pendingPayments})", 'url' => route('admin.orders.index'), 'style' => 'green'];
        return $this->r($text, $actions);
    }

    private function pointsStats(): array
    {
        $totalPoints = \App\Models\Wallet::sum('points');
        $totalBalance = \App\Models\Wallet::sum('balance');
        $walletsCount = \App\Models\Wallet::where('points', '>', 0)->orWhere('balance', '>', 0)->count();
        $topPoints = \App\Models\Wallet::with('user:id,first_name,last_name')->orderByDesc('points')->take(5)->get();

        $lines = $topPoints->map(function ($w) {
            $name = $w->user ? ($w->user->first_name . ' ' . $w->user->last_name) : '?';
            return "- {$name} | {$w->points} نقطة | " . number_format($w->balance) . " ج.م رصيد";
        })->implode("\n");

        return $this->r("النقاط والمحفظة:\nإجمالي النقاط الموزعة: " . number_format($totalPoints) . "\nإجمالي أرصدة المحافظ: " . number_format($totalBalance) . " ج.م\nعملاء لديهم رصيد/نقاط: {$walletsCount}\n\nأكثر العملاء نقاطاً:\n{$lines}");
    }

    // ========== Status Helpers ==========

    private function statusAr(string $status): string
    {
        return match ($status) {
            'PENDING' => 'معلق',
            'CONFIRMED' => 'مؤكد',
            'PROCESSING' => 'قيد التجهيز',
            'SHIPPED' => 'تم الشحن',
            'DELIVERED' => 'تم التوصيل',
            'CANCELLED' => 'ملغي',
            'RETURNED' => 'مرتجع',
            default => $status,
        };
    }

    private function paymentStatusAr(string $status): string
    {
        return match ($status) {
            'PENDING' => 'في الانتظار',
            'PAID' => 'مدفوع',
            'FAILED' => 'فشل',
            'REFUNDED' => 'مسترد',
            default => $status,
        };
    }
}
