<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', ProductController::class);
    Route::get('products/{product}/json', [ProductController::class, 'getProduct'])->name('products.json');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/finalize', [InvoiceController::class, 'finalize'])->name('invoices.finalize');
    Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::post('invoices/{invoice}/items', [InvoiceController::class, 'addItem'])->name('invoice.items.store');
    Route::delete('invoice-items/{item}', [InvoiceController::class, 'removeItem'])->name('invoice.items.destroy');
    Route::patch('invoice-items/{item}', [InvoiceController::class, 'updateItem'])->name('invoice.items.update');

    // Payments
    Route::get('invoices/{invoice}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
});

require __DIR__.'/auth.php';
