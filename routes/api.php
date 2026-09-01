<?php

use App\Http\Controllers\RazorpayWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are stateless and do not include CSRF protection by default.
| Razorpay webhook is exposed here so external calls are accepted securely.
|
*/

use App\Http\Controllers\BookingController;

Route::post('/razorpay/webhook', [RazorpayWebhookController::class, 'handle'])->name('api.razorpay.webhook');

// Razorpay Standard Checkout API endpoints
Route::post('/create-order', [BookingController::class, 'createOrderApi'])->name('api.razorpay.createOrder');
Route::post('/verify-payment', [BookingController::class, 'verifyPaymentApi'])->name('api.razorpay.verifyPayment');
