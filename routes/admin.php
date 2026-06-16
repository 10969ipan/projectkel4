<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\PharmacareAdminController;

/*
|--------------------------------------------------------------------------
| Backoffice & Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'backoffice', 'menu_permission'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update'); 

    // Transactions
    Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store']);

    // PHARMACARE STORE ROUTES
    Route::get('/pharmacare/transactions', [PharmacareAdminController::class, 'transactions'])->name('admin.pharmacare.transactions');
    Route::get('/pharmacare/transaction-logs', [PharmacareAdminController::class, 'transactionLogs'])->name('admin.pharmacare.transaction-logs');
    Route::get('/pharmacare/transaction-logs/download', [PharmacareAdminController::class, 'downloadTransactionLogs'])->name('admin.pharmacare.transaction-logs.download');
    Route::put('/pharmacare/transactions/{id}', [PharmacareAdminController::class, 'updateTransaction'])->name('admin.pharmacare.transactions.update');

    // Dynamic Permission Controlled Routes (Formerly static admin-only)
    // User Management
    Route::resource('users', UserController::class)->except(['show']);

    // Categories
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Units
    Route::resource('units', UnitController::class)->except(['show']);

    // Items Management (Full CRUD)
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

    // PHARMACARE ADMIN ROUTES
    Route::get('/pharmacare', [PharmacareAdminController::class, 'index'])->name('admin.pharmacare.index');
    Route::get('/pharmacare/customers', [PharmacareAdminController::class, 'customers'])->name('admin.pharmacare.customers');
    Route::put('/pharmacare/customers/{id}', [PharmacareAdminController::class, 'updateCustomer'])->name('admin.pharmacare.customers.update');
    Route::post('/pharmacare/approve/{userId}', [PharmacareAdminController::class, 'approvePrescription'])->name('admin.pharmacare.approve');
    Route::post('/pharmacare/paylater/{userId}', [PharmacareAdminController::class, 'updatePaylater'])->name('admin.pharmacare.paylater');
    Route::get('/pharmacare/transactions/{id}/invoice', [PharmacareAdminController::class, 'showInvoice'])->name('admin.pharmacare.invoice');

    // Items (Accessible by Staff & Admin)
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

    // Item Requests
    Route::resource('item-requests', ItemRequestController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/item-requests/{item_request}/approve', [ItemRequestController::class, 'approve'])->name('item-requests.approve');
    Route::post('/item-requests/{item_request}/reject', [ItemRequestController::class, 'reject'])->name('item-requests.reject');
});
