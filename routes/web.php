<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StoreController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ConsultationController;

// EMERGENCY DATA FIX (Visit /fix-roles to fix customer categories)
Route::get('/fix-roles', function() {
    \App\Models\User::where('store_role', 'customer')->update(['role' => null]);
    return redirect()->route('store.index')->with('success', 'Database Pelanggan berhasil diperbaiki!');
});

// Authentication Routes
// Authentication & Home Redirection
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'staff') {
            return redirect()->route('dashboard');
        }
        return redirect()->route('store.index');
    }
    return view('auth.login');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// PHARMACARE E-COMMERCE (STORE FRONT)
// ==========================================
Route::get('/store', [StoreController::class, 'index'])->name('store.index');
Route::get('/store/item/{id}', [StoreController::class, 'show'])->name('store.show');

// Store Auth (Login / Register Pharmacare)
Route::get('/store/login', [\App\Http\Controllers\StoreAuthController::class, 'showLogin'])->name('store.login');
Route::post('/store/login', [\App\Http\Controllers\StoreAuthController::class, 'login'])->name('store.login.post');
Route::get('/store/register', [\App\Http\Controllers\StoreAuthController::class, 'showRegister'])->name('store.register');
Route::post('/store/register', [\App\Http\Controllers\StoreAuthController::class, 'register'])->name('store.register.store');
Route::post('/store/logout', [\App\Http\Controllers\StoreAuthController::class, 'logout'])->name('store.logout');

// Telemedicine AI (Public)
Route::post('/consultation/ai-reply', [ConsultationController::class, 'aiReply'])->name('telemedicine.ai-reply');

// Telemedicine Directory (Guest Or Auth)
Route::get('/telemedicine', [ConsultationController::class, 'index'])->name('telemedicine.index');

Route::middleware(['auth', 'customer'])->group(function () {
    // Checkout Process
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Cart Routes
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{itemId}', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{itemId}', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{itemId}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

    // Telemedicine Chat & Actions
    Route::get('/telemedicine/chat/{doctorId}', [ConsultationController::class, 'chat'])->name('telemedicine.chat');
    Route::post('/telemedicine/chat/{doctorId}', [ConsultationController::class, 'storeMessage'])->name('telemedicine.store_message');
    Route::post('/telemedicine/approve/{userId}', [ConsultationController::class, 'approvePrescription'])->name('telemedicine.approve');
    
    // User Customer Dashboard
    Route::get('/account/orders', [\App\Http\Controllers\AccountController::class, 'index'])->name('account.orders');
    Route::get('/account/dashboard', [\App\Http\Controllers\AccountController::class, 'index'])->name('account.dashboard'); // alias
    Route::get('/account/profile', [\App\Http\Controllers\AccountController::class, 'showProfile'])->name('account.profile');
    Route::put('/account/profile', [\App\Http\Controllers\AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::put('/account/password', [\App\Http\Controllers\AccountController::class, 'changePassword'])->name('account.password.update');

    // Order Payment & Cancellation
    Route::get('/account/orders/{id}/pay', [\App\Http\Controllers\AccountController::class, 'showPayment'])->name('account.orders.pay');
    Route::post('/account/orders/pay-new', [\App\Http\Controllers\AccountController::class, 'processPayment'])->name('account.orders.pay.new');
    Route::post('/account/orders/{id}/pay', [\App\Http\Controllers\AccountController::class, 'processPayment'])->name('account.orders.pay.post');
    Route::delete('/account/orders/{id}', [\App\Http\Controllers\AccountController::class, 'cancelOrder'])->name('account.orders.cancel');
    
    // Address Management
    Route::post('/account/address', [\App\Http\Controllers\AccountController::class, 'storeAddress'])->name('account.address.store');
    Route::put('/account/address/{id}', [\App\Http\Controllers\AccountController::class, 'updateAddress'])->name('account.address.update');
    Route::delete('/account/address/{id}', [\App\Http\Controllers\AccountController::class, 'deleteAddress'])->name('account.address.delete');
    Route::post('/account/address/{id}/primary', [\App\Http\Controllers\AccountController::class, 'setPrimaryAddress'])->name('account.address.primary');
});

// Authenticated Routes
Route::middleware(['auth', 'backoffice'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // --- PROFILE ROUTES ---
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update'); 
    // ----------------------

    // Admin Only Routes
    Route::middleware('admin')->group(function () {
        // User Management
        Route::resource('users', UserController::class)->except(['show']);

        // Categories
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Units
        Route::resource('units', UnitController::class)->except(['show']);

        // Items Management (Admin Only)
        Route::resource('items', ItemController::class)->except(['index', 'show']);

        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
            Route::get('/stock/download', [ReportController::class, 'downloadStockReport'])->name('reports.stock.download');
            Route::get('/transactions', [ReportController::class, 'transactionReport'])->name('reports.transactions');
            Route::get('/transactions/download', [ReportController::class, 'downloadTransactionReport'])->name('reports.transactions.download');
            Route::get('/requests', [ReportController::class, 'requestReport'])->name('reports.requests');
            Route::get('/requests/download', [ReportController::class, 'downloadRequestReport'])->name('reports.requests.download');
        });

        Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store']);

        // PHARMACARE ADMIN ROUTES
        Route::get('/pharmacare', [\App\Http\Controllers\PharmacareAdminController::class, 'index'])->name('admin.pharmacare.index');
        Route::get('/pharmacare/transactions', [\App\Http\Controllers\PharmacareAdminController::class, 'transactions'])->name('admin.pharmacare.transactions');
        Route::get('/pharmacare/transaction-logs', [\App\Http\Controllers\PharmacareAdminController::class, 'transactionLogs'])->name('admin.pharmacare.transaction-logs');
        Route::put('/pharmacare/transactions/{id}', [\App\Http\Controllers\PharmacareAdminController::class, 'updateTransaction'])->name('admin.pharmacare.transactions.update');
        Route::get('/pharmacare/customers', [\App\Http\Controllers\PharmacareAdminController::class, 'customers'])->name('admin.pharmacare.customers');
        Route::put('/pharmacare/customers/{id}', [\App\Http\Controllers\PharmacareAdminController::class, 'updateCustomer'])->name('admin.pharmacare.customers.update');
        Route::post('/pharmacare/approve/{userId}', [\App\Http\Controllers\PharmacareAdminController::class, 'approvePrescription'])->name('admin.pharmacare.approve');
        Route::post('/pharmacare/paylater/{userId}', [\App\Http\Controllers\PharmacareAdminController::class, 'updatePaylater'])->name('admin.pharmacare.paylater');
    });

    // Items (Accessible by Staff & Admin)
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

    // Item Requests
    Route::resource('item-requests', ItemRequestController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/item-requests/{item_request}/approve', [ItemRequestController::class, 'approve'])->name('item-requests.approve');
    Route::post('/item-requests/{item_request}/reject', [ItemRequestController::class, 'reject'])->name('item-requests.reject');
});