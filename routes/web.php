<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\RazorpayWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\VendorPackageController;
use App\Http\Controllers\VendorRegistrationController;
use Illuminate\Support\Facades\Route;

// Public Frontend Pages (SEO & Schema Powered)
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/gallery', [FrontendController::class, 'gallery'])->name('gallery');
Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [FrontendController::class, 'blogSingle'])->name('blog.single');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::post('/contact', [FrontendController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/terms', [FrontendController::class, 'terms'])->name('terms');
Route::get('/disclaimer', [FrontendController::class, 'disclaimer'])->name('disclaimer');

// Booking & Checkout Flow
Route::get('/package/{slug}/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');
Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
Route::post('/booking/callback', [BookingController::class, 'callback'])->name('booking.callback');

// Phone / SMS OTP Verification Endpoints
Route::post('/otp/send', [\App\Http\Controllers\OtpController::class, 'send'])->name('otp.send');
Route::post('/otp/verify', [\App\Http\Controllers\OtpController::class, 'verify'])->name('otp.verify');
Route::post('/otp/resend', [\App\Http\Controllers\OtpController::class, 'resend'])->name('otp.resend');

// Client Dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/client/dashboard', [ClientDashboardController::class, 'index'])->name('client.dashboard');
});

// Vendor Registration & Dashboard
Route::get('/vendor/register', [VendorRegistrationController::class, 'show'])->name('vendor.register.show');
Route::post('/vendor/register', [VendorRegistrationController::class, 'register'])->name('vendor.register');

Route::prefix('vendor')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('vendor.dashboard');
    Route::get('/packages', [VendorPackageController::class, 'index'])->name('vendor.packages.index');
    Route::get('/packages/create', [VendorPackageController::class, 'create'])->name('vendor.packages.create');
    Route::post('/packages', [VendorPackageController::class, 'store'])->name('vendor.packages.store');
    Route::get('/packages/{package}/edit', [VendorPackageController::class, 'edit'])->name('vendor.packages.edit');
    Route::patch('/packages/{package}', [VendorPackageController::class, 'update'])->name('vendor.packages.update');
    Route::delete('/packages/{package}', [VendorPackageController::class, 'destroy'])->name('vendor.packages.destroy');
});

// Super Admin Management & Theme Customizer
Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\EnsureAdmin::class])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Dynamic Settings & Theme Color Customizer
    Route::post('/settings', [AdminDashboardController::class, 'saveSettings'])->name('admin.settings.save');
    Route::post('/settings/theme-preset', [AdminDashboardController::class, 'applyThemePreset'])->name('admin.settings.preset');
    Route::post('/sms/test', [AdminDashboardController::class, 'testSms'])->name('admin.sms.test');
    
    // Bookings Management
    Route::post('/booking/{booking}/status', [AdminDashboardController::class, 'updateBookingStatus'])->name('admin.booking.updateStatus');
    Route::delete('/booking/{booking}', [AdminDashboardController::class, 'deleteBooking'])->name('admin.booking.delete');

    // Packages Management
    Route::post('/package', [AdminDashboardController::class, 'storePackage'])->name('admin.package.store');
    Route::patch('/package/{package}', [AdminDashboardController::class, 'updatePackage'])->name('admin.package.update');
    Route::delete('/package/{package}', [AdminDashboardController::class, 'deletePackage'])->name('admin.package.delete');

    // Blog Management
    Route::post('/blog', [AdminDashboardController::class, 'storeBlog'])->name('admin.blog.store');
    Route::delete('/blog/{blog}', [AdminDashboardController::class, 'deleteBlog'])->name('admin.blog.delete');

    // Gallery Management
    Route::post('/gallery', [AdminDashboardController::class, 'storeGallery'])->name('admin.gallery.store');
    Route::delete('/gallery/{gallery}', [AdminDashboardController::class, 'deleteGallery'])->name('admin.gallery.delete');

    // Vendor Partners Management
    Route::get('/vendors', [AdminDashboardController::class, 'vendorsList'])->name('admin.vendors.list');
    Route::post('/vendor/{vendor}/status', [AdminDashboardController::class, 'updateVendorStatus'])->name('admin.vendor.updateStatus');

    // Messages Inbox
    Route::post('/message/{message}/read', [AdminDashboardController::class, 'markMessageRead'])->name('admin.message.read');
    Route::delete('/message/{message}', [AdminDashboardController::class, 'deleteMessage'])->name('admin.message.delete');
});

// Razorpay Webhooks
Route::post('/razorpay/webhook', [RazorpayWebhookController::class, 'handle'])->name('razorpay.webhook');
Route::post('/webhooks/razorpay', [RazorpayWebhookController::class, 'handle'])->name('razorpay.webhook.alias');

// SEO XML Sitemap & Robots
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.xml');
Route::get('/robots.txt', function() {
    $appUrl = config('app.url') ?: env('APP_URL', 'http://localhost/vk/studio/');
    $content = "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /client/\nDisallow: /vendor/\n\nSitemap: {$appUrl}/sitemap.xml\n";
    return response($content, 200)->header('Content-Type', 'text/plain');
});

// Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Role-based dynamic dashboard redirect
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user && $user->isVendor()) {
        return redirect()->route('vendor.dashboard');
    }
    return redirect()->route('client.dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
