<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\WishlistController;
use App\Http\Controllers\Shop\AccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\AiAssistantController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Shop\ContactController;

// ==================== WEBHOOKS (no CSRF) ====================
Route::post('/webhooks/bosta', [\App\Http\Controllers\Webhook\BostaWebhookController::class, 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhooks.bosta');

// ==================== AUTH ====================
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
Route::post('/register', [LoginController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ==================== STOREFRONT ====================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

// Cart (works for guests via session)
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::delete('/cart/{key}', [CartController::class, 'remove'])->name('cart.remove');

// Checkout (works for guests - enter info at checkout)
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');
Route::get('/order/{order}/success', [OrderController::class, 'success'])->name('order.success');
Route::get('/order/{order}/track', [OrderController::class, 'track'])->name('order.track');
Route::get('/track/{orderNumber}', [OrderController::class, 'publicTrack'])->name('order.public-track');

// Shipping cost API
Route::get('/api/shipping-cost', [\App\Http\Controllers\Admin\ShippingController::class, 'getCost'])->name('api.shipping.cost');

// Coupon validation API
Route::post('/api/coupon/validate', function (\Illuminate\Http\Request $request) {
    $coupon = \App\Models\Coupon::where('code', $request->code)->first();
    if (!$coupon) return response()->json(['valid' => false, 'error' => 'كود الخصم غير صحيح']);
    $subtotal = (float) ($request->subtotal ?? 0);
    if (!$coupon->isValid($subtotal)) {
        if ($coupon->min_order_value && $subtotal < $coupon->min_order_value)
            return response()->json(['valid' => false, 'error' => 'الحد الأدنى للطلب ' . number_format($coupon->min_order_value) . ' ج.م']);
        return response()->json(['valid' => false, 'error' => 'كود الخصم منتهي أو غير صالح']);
    }
    $discount = $coupon->calculateDiscount($subtotal);
    $label = $coupon->type === 'PERCENTAGE' ? $coupon->value . '%' : number_format($discount) . ' ج.م';
    return response()->json(['valid' => true, 'discount' => $discount, 'label' => $label, 'type' => $coupon->type]);
})->name('api.coupon.validate');

// Contact form
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Authenticated customer routes
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::put('/account', [AccountController::class, 'updateProfile'])->name('account.update');
    Route::post('/account/address', [AccountController::class, 'storeAddress'])->name('account.address.store');
    Route::delete('/account/address/{address}', [AccountController::class, 'deleteAddress'])->name('account.address.delete');

    // Points & Wallet
    Route::post('/account/redeem-points', [AccountController::class, 'redeemPoints'])->name('account.redeem-points');
});

// ==================== ADMIN ====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/ai-assistant', [AiAssistantController::class, 'query'])->name('ai-assistant');

    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
    Route::resource('coupons', CouponController::class)->except(['show', 'create', 'edit']);

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/shipping', [AdminOrderController::class, 'updateShipping'])->name('orders.shipping');
    Route::patch('/orders/{order}/verify-payment', [AdminOrderController::class, 'verifyPayment'])->name('orders.verify-payment');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{user}', [CustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers/{user}/wallet', [\App\Http\Controllers\Admin\WalletController::class, 'update'])->name('customers.wallet');

    // POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/session/open', [PosController::class, 'openSession'])->name('pos.session.open');
    Route::post('/pos/session/close', [PosController::class, 'closeSession'])->name('pos.session.close');
    Route::get('/pos/search', [PosController::class, 'searchProducts'])->name('pos.search');
    Route::post('/pos/transaction', [PosController::class, 'createTransaction'])->name('pos.transaction');
    Route::post('/pos/return', [PosController::class, 'processReturn'])->name('pos.return');
    Route::get('/pos/shifts', [PosController::class, 'shifts'])->name('pos.shifts');
    Route::get('/pos/returns', [PosController::class, 'returns'])->name('pos.returns');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

    // Shipping rates
    Route::get('/shipping', [\App\Http\Controllers\Admin\ShippingController::class, 'index'])->name('shipping.index');
    Route::post('/shipping', [\App\Http\Controllers\Admin\ShippingController::class, 'store'])->name('shipping.store');
    Route::put('/shipping/{shippingRate}', [\App\Http\Controllers\Admin\ShippingController::class, 'update'])->name('shipping.update');
    Route::delete('/shipping/{shippingRate}', [\App\Http\Controllers\Admin\ShippingController::class, 'destroy'])->name('shipping.destroy');
    Route::post('/shipping/sync-bosta', [\App\Http\Controllers\Admin\ShippingController::class, 'syncFromBosta'])->name('shipping.sync-bosta');
    Route::get('/shipping/bosta-cost', [\App\Http\Controllers\Admin\ShippingController::class, 'getBostaCost'])->name('shipping.bosta-cost');

    // Banners
    Route::get('/banners', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('banners.store');
    Route::put('/banners/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('banners.destroy');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Messages
    Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');
});
