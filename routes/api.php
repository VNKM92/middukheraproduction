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

Route::post('/razorpay/webhook', [RazorpayWebhookController::class, 'handle'])->name('api.razorpay.webhook');

// Add additional API endpoints below as needed
