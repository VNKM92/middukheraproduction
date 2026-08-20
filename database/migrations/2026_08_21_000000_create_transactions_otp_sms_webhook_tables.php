<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Transactions Table - Comprehensive Payment Tracking
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_ref')->unique(); // e.g. TRX-66C0F8...
                $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->decimal('amount', 12, 2);
                $table->string('currency', 10)->default('INR');
                $table->string('status')->default('initiated'); // initiated, pending_otp, otp_verified, processing, captured, failed, refunded
                $table->string('payment_method')->nullable(); // card, upi, netbanking, wallet, instant_simulation
                $table->string('razorpay_order_id')->nullable()->index();
                $table->string('razorpay_payment_id')->nullable()->index();
                $table->string('razorpay_signature')->nullable();
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_phone')->nullable();
                $table->text('failure_reason')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->json('raw_response')->nullable();
                $table->timestamps();
            });
        }

        // 2. OTP Verifications Table
        if (!Schema::hasTable('otp_verifications')) {
            Schema::create('otp_verifications', function (Blueprint $table) {
                $table->id();
                $table->string('phone')->nullable()->index();
                $table->string('email')->nullable()->index();
                $table->string('otp_code');
                $table->string('token')->unique();
                $table->string('action')->default('booking_verification'); // booking_verification, login, payment_confirm
                $table->string('status')->default('pending'); // pending, verified, expired
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->timestamp('expires_at');
                $table->timestamp('verified_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }

        // 3. SMS Logs Table - Custom SMS Delivery Tracking
        if (!Schema::hasTable('sms_logs')) {
            Schema::create('sms_logs', function (Blueprint $table) {
                $table->id();
                $table->string('recipient');
                $table->text('message');
                $table->string('driver')->default('simulation'); // twilio, fast2sms, msg91, custom_http, log
                $table->string('template_key')->nullable(); // otp, payment_success, payment_failed, admin_alert
                $table->string('status')->default('sent'); // sent, failed, simulated
                $table->text('response_payload')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }

        // 4. Webhook Logs Table - Webhook Tracking & Idempotency
        if (!Schema::hasTable('webhook_logs')) {
            Schema::create('webhook_logs', function (Blueprint $table) {
                $table->id();
                $table->string('event_id')->nullable()->index();
                $table->string('event_type')->index(); // payment.captured, payment.failed, order.paid, etc.
                $table->string('signature')->nullable();
                $table->boolean('is_valid_signature')->default(true);
                $table->boolean('processed')->default(false);
                $table->string('status_message')->nullable();
                $table->json('payload')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }

        // Add customer_phone to bookings table if not present
        if (Schema::hasTable('bookings') && !Schema::hasColumn('bookings', 'customer_phone')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('customer_phone')->nullable()->after('notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('otp_verifications');
        Schema::dropIfExists('transactions');

        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'customer_phone')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('customer_phone');
            });
        }
    }
};
