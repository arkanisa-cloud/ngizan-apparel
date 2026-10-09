<?php

use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\ShopController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{product:slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/contact', [ShopController::class, 'contact'])->name('contact');

// Legacy aliases
Route::get('/customer/shop', [ShopController::class, 'index'])->name('customer.shop.index');
Route::get('/customer/product/{product}', [ShopController::class, 'show'])->name('customer.shop.show');

// API Shipping (Biteship & OSM Geocoding)
Route::get('/api/shipping/areas', [CheckoutController::class, 'searchAreas'])->name('api.shipping.areas');
Route::get('/api/shipping/reverse-geocode', [CheckoutController::class, 'reverseGeocode'])->name('api.shipping.reverse-geocode');
Route::post('/api/shipping/rates', [CheckoutController::class, 'getRates'])->name('api.shipping.rates');

// Dashboard Dispatcher
Route::get('/dashboard', function () {
    return auth()->user()->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

// Webhook Midtrans
Route::post('/api/webhooks/midtrans', [\App\Http\Controllers\Webhooks\MidtransWebhookController::class, 'handle'])
    ->name('api.webhooks.midtrans');

// ==========================================
// ADMIN BACKOFFICE ROUTES
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Master Kategori
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

    // Master Banner & Hero Section
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class)->only(['index', 'edit', 'update']);

    // Master Produk & Varian Matrix
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);

    // Master Panduan Ukuran (Size Charts)
    Route::resource('size-charts', \App\Http\Controllers\Admin\SizeChartController::class);

    // Inventori & Mutasi Stok
    Route::resource('stock-ins', \App\Http\Controllers\Admin\StockInController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('stock-outs', \App\Http\Controllers\Admin\StockOutController::class)->only(['index', 'create', 'store', 'show']);

    // Manajemen Pesanan & Logistik
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/tracking', [\App\Http\Controllers\Admin\OrderController::class, 'updateTracking'])->name('orders.tracking');
    Route::post('/orders/verify-tracking', [\App\Http\Controllers\Admin\OrderController::class, 'verifyTracking'])->name('orders.verify.tracking');
    Route::get('/orders/{order}/print-label', [\App\Http\Controllers\Admin\OrderController::class, 'printShippingLabel'])->name('orders.print.label');

    // Manajemen Pengguna & Sesi Akun
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('users.role');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Laporan & Rekap Finansial
    Route::get('/reports/sales', [\App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/stock', [\App\Http\Controllers\Admin\ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/export-sales', [\App\Http\Controllers\Admin\ReportController::class, 'exportSalesCsv'])->name('reports.export.sales');

    // Profil Admin & Keamanan Akun
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Orders & Tracking
    Route::get('/orders', [CheckoutController::class, 'indexOrders'])->name('orders.index');
    Route::get('/orders/{order}', [CheckoutController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{order}/sync-payment', [CheckoutController::class, 'syncPayment'])->name('orders.sync-payment');

    // Shipping Addresses
    Route::patch('/addresses/{address}/primary', [\App\Http\Controllers\Customer\ShippingAddressController::class, 'setPrimary'])->name('addresses.primary');
    Route::resource('addresses', \App\Http\Controllers\Customer\ShippingAddressController::class);

    // Ngizan Premium Subscription
    Route::post('/premium/subscribe', [\App\Http\Controllers\Customer\PremiumSubscriptionController::class, 'store'])->name('premium.subscribe');
    Route::post('/premium/sync', [\App\Http\Controllers\Customer\PremiumSubscriptionController::class, 'sync'])->name('premium.sync');

    // Product Reviews (Verified Buyer)
    Route::post('/reviews', [\App\Http\Controllers\Customer\ReviewController::class, 'store'])->name('reviews.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
