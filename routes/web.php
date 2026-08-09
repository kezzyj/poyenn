<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::redirect('/dashboard', '/')->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\Shop\CategoryController as ShopCategoryController;

// ================================
// CUSTOMER STOREFRONT (PUBLIC)
// ================================
Route::name('shop.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/products', [ShopProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [ShopProductController::class, 'show'])->name('products.show');
    Route::get('/categories/{category:slug}', [ShopCategoryController::class, 'show'])->name('categories.show');
    Route::get('/track', [\App\Http\Controllers\Shop\OrderController::class, 'track'])->name('orders.track');
    
    // Cart routes — require auth
    Route::middleware('auth')->group(function () {
        Route::get('/cart', [\App\Http\Controllers\Shop\CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{product}', [\App\Http\Controllers\Shop\CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/items/{cartItem}', [\App\Http\Controllers\Shop\CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/items/{cartItem}', [\App\Http\Controllers\Shop\CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/cart', [\App\Http\Controllers\Shop\CartController::class, 'clear'])->name('cart.clear');
        Route::get('/checkout', [\App\Http\Controllers\Shop\CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout/addresses', [\App\Http\Controllers\Shop\CheckoutController::class, 'storeAddress'])->name('checkout.address.store');
        Route::get('/checkout/zones/{deliveryZone}/rates', [\App\Http\Controllers\Shop\CheckoutController::class, 'getDeliveryRates'])->name('checkout.zone.rates');
        Route::post('/checkout/place-order', [\App\Http\Controllers\Shop\CheckoutController::class, 'placeOrder'])->name('checkout.place-order');
        Route::get('/orders', [\App\Http\Controllers\Shop\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Shop\OrderController::class, 'show'])->name('orders.show');
        Route::get('/payment/callback', [\App\Http\Controllers\Shop\PaymentController::class, 'callback'])->name('payment.callback');
        Route::post('/payment/retry/{order}', [\App\Http\Controllers\Shop\PaymentController::class, 'retry'])->name('payment.retry');
    });
});

// ================================
// ADMIN ROUTES
// ================================
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest routes (not logged in)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    // Protected routes (logged in)
    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::post('categories/{category}/toggle-status', [\App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
        Route::post('brands/{brand}/toggle-status', [\App\Http\Controllers\Admin\BrandController::class, 'toggleStatus'])->name('brands.toggle-status');
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        Route::post('products/{product}/toggle-status', [\App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::resource('delivery-zones', \App\Http\Controllers\Admin\DeliveryZoneController::class);
        Route::post('delivery-zones/{deliveryZone}/rates', [\App\Http\Controllers\Admin\DeliveryRateController::class, 'store'])->name('delivery-rates.store');
        Route::put('delivery-rates/{deliveryRate}', [\App\Http\Controllers\Admin\DeliveryRateController::class, 'update'])->name('delivery-rates.update');
        Route::delete('delivery-rates/{deliveryRate}', [\App\Http\Controllers\Admin\DeliveryRateController::class, 'destroy'])->name('delivery-rates.destroy');
        Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('orders/{order}/assign-agent', [\App\Http\Controllers\Admin\OrderController::class, 'assignAgent'])->name('orders.assign-agent');
        Route::post('orders/{order}/mark-paid', [\App\Http\Controllers\Admin\OrderController::class, 'markPaid'])->name('orders.mark-paid');
        Route::post('orders/{order}/note', [\App\Http\Controllers\Admin\OrderController::class, 'addNote'])->name('orders.add-note');
        Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
        Route::resource('delivery-agents', \App\Http\Controllers\Admin\DeliveryAgentController::class)->except(['show']);
        Route::post('delivery-agents/{deliveryAgent}/toggle-status', [\App\Http\Controllers\Admin\DeliveryAgentController::class, 'toggleStatus'])->name('delivery-agents.toggle-status');
        Route::get('customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});

use App\Http\Controllers\Agent\AuthController as AgentAuthController;

// ================================
// DELIVERY AGENT ROUTES
// ================================
Route::prefix('agent')->name('agent.')->group(function () {

    // Guest routes (not logged in)
    Route::middleware('guest:agent')->group(function () {
        Route::get('login', [AgentAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AgentAuthController::class, 'login'])->name('login.submit');
    });

    // Protected routes (logged in)
        Route::middleware('auth:agent')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Agent\DashboardController::class, 'index'])->name('dashboard');
        Route::get('history', [\App\Http\Controllers\Agent\DashboardController::class, 'history'])->name('history');
        Route::get('deliveries/{delivery}', [\App\Http\Controllers\Agent\DashboardController::class, 'show'])->name('delivery.show');

        Route::post('deliveries/{delivery}/pick-up', [\App\Http\Controllers\Agent\DashboardController::class, 'pickUp'])->name('delivery.pick-up');
        Route::post('deliveries/{delivery}/in-transit', [\App\Http\Controllers\Agent\DashboardController::class, 'inTransit'])->name('delivery.in-transit');
        Route::post('deliveries/{delivery}/deliver', [\App\Http\Controllers\Agent\DashboardController::class, 'deliver'])->name('delivery.deliver');
        Route::post('deliveries/{delivery}/fail', [\App\Http\Controllers\Agent\DashboardController::class, 'fail'])->name('delivery.fail');

        Route::post('logout', [AgentAuthController::class, 'logout'])->name('logout');
    });
});

require __DIR__.'/auth.php';
