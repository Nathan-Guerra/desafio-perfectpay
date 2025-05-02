<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::get('/payments/{paymentUuid}', [PaymentController::class, 'show'])->name('payments.show');
