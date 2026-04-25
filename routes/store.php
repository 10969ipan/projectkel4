<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\StoreAuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Pharmacare Storefront Routes
|--------------------------------------------------------------------------
*/

// Google OAuth — HARUS di luar semua middleware agar tidak terhalang session/auth check
Route::get('/auth/google', [StoreAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [StoreAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Midtrans Webhook Callback
Route::post('/midtrans/callback', [\App\Http\Controllers\MidtransController::class, 'callback'])->name('midtrans.callback');

Route::middleware(['customer'])->group(function () {
    Route::get('/store', [StoreController::class, 'index'])->name('store.index');
    Route::get('/store/item/{id}', [StoreController::class, 'show'])->name('store.show');
    
    // Store Auth (Login / Register Pharmacare)
    Route::get('/store/login', [StoreAuthController::class, 'showLogin'])->name('store.login');
    Route::post('/store/login', [StoreAuthController::class, 'login'])->name('store.login.post');
    Route::get('/store/register', [StoreAuthController::class, 'showRegister'])->name('store.register');
    Route::post('/store/register', [StoreAuthController::class, 'register'])->name('store.register.store');
    Route::post('/store/logout', [StoreAuthController::class, 'logout'])->name('store.logout');

    // Telemedicine Directory & AI (Public access but role-checked)
    Route::get('/telemedicine', [ConsultationController::class, 'index'])->name('telemedicine.index');
    Route::post('/consultation/ai-reply', [ConsultationController::class, 'aiReply'])->name('telemedicine.ai-reply');

    // AJAX Search for Autocomplete
    Route::get('/store/search-ajax', [StoreController::class, 'searchAjax'])->name('store.search-ajax');

    // Cart AJAX API
    Route::get('/cart/summary', [CartController::class, 'summary'])->name('cart.summary');
    Route::post('/cart/add-ajax/{itemId}', [CartController::class, 'add'])->name('cart.add-ajax');

    // Quick View API
    Route::get('/store/quick-view/{id}', [StoreController::class, 'quickView'])->name('store.quick-view');

    // Notifications API
    Route::get('/api/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/api/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    // RajaOngkir API Integration (Minggu Ke-7)
    Route::get('/cek-ongkir', [\App\Http\Controllers\RajaOngkirController::class, 'index'])->name('ongkir.index');
    Route::get('/provinces', [\App\Http\Controllers\RajaOngkirController::class, 'getProvinces'])->name('ongkir.provinces');
    Route::get('/cities', [\App\Http\Controllers\RajaOngkirController::class, 'getCities'])->name('ongkir.cities');
    Route::post('/cost', [\App\Http\Controllers\RajaOngkirController::class, 'getCost'])->name('ongkir.cost');
});

Route::middleware(['auth', 'customer'])->group(function () {
    // Checkout Process
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{itemId}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{itemId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Telemedicine Chat & Actions
    Route::get('/telemedicine/chat/{doctorId}', [ConsultationController::class, 'chat'])->name('telemedicine.chat');
    Route::post('/telemedicine/chat/{doctorId}', [ConsultationController::class, 'storeMessage'])->name('telemedicine.store_message');
    Route::post('/telemedicine/approve/{userId}', [ConsultationController::class, 'approvePrescription'])->name('telemedicine.approve');
    
    // User Customer Dashboard
    Route::get('/account/orders', [AccountController::class, 'index'])->name('account.orders');
    Route::get('/account/dashboard', [AccountController::class, 'index'])->name('account.dashboard'); // alias
    Route::get('/account/profile', [AccountController::class, 'showProfile'])->name('account.profile');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::put('/account/password', [AccountController::class, 'changePassword'])->name('account.password.update');

    // Wallet & Subscriptions
    Route::get('/account/wallet', [AccountController::class, 'showWallet'])->name('account.wallet');
    Route::post('/account/wallet/topup', [AccountController::class, 'topUp'])->name('account.wallet.topup');

    // Order Payment & Cancellation
    Route::get('/account/orders/{id}/pay', [AccountController::class, 'showPayment'])->name('account.orders.pay');
    Route::post('/account/orders/pay-new', [AccountController::class, 'processPayment'])->name('account.orders.pay.new');
    Route::post('/account/orders/{id}/pay', [AccountController::class, 'processPayment'])->name('account.orders.pay.post');
    Route::get('/account/orders/{id}/check-status', [AccountController::class, 'checkPaymentStatus'])->name('account.orders.check_status');
    Route::delete('/account/orders/{id}', [AccountController::class, 'cancelOrder'])->name('account.orders.cancel');
    
    // Address Management
    Route::post('/account/address', [AccountController::class, 'storeAddress'])->name('account.address.store');
    Route::put('/account/address/{id}', [AccountController::class, 'updateAddress'])->name('account.address.update');
    Route::delete('/account/address/{id}', [AccountController::class, 'deleteAddress'])->name('account.address.delete');
    Route::post('/account/address/{id}/primary', [AccountController::class, 'setPrimaryAddress'])->name('account.address.primary');
    Route::get('/account/orders/{id}/invoice', [AccountController::class, 'showInvoice'])->name('account.orders.invoice');
    Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
});
