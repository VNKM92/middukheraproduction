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
        // 1. Update Transactions Table
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'gateway')) {
                    $table->string('gateway', 30)->default('razorpay')->after('currency');
                }
                if (!Schema::hasColumn('transactions', 'cashfree_order_id')) {
                    $table->string('cashfree_order_id')->nullable()->index()->after('razorpay_signature');
                }
                if (!Schema::hasColumn('transactions', 'cashfree_payment_id')) {
                    $table->string('cashfree_payment_id')->nullable()->index()->after('cashfree_order_id');
                }
                if (!Schema::hasColumn('transactions', 'payment_session_id')) {
                    $table->string('payment_session_id')->nullable()->after('cashfree_payment_id');
                }
            });
        }

        // 2. Update Bookings Table
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('bookings', 'payment_gateway')) {
                    $table->string('payment_gateway', 30)->nullable()->after('customer_phone');
                }
                if (!Schema::hasColumn('bookings', 'cashfree_order_id')) {
                    $table->string('cashfree_order_id')->nullable()->index()->after('razorpay_signature');
                }
                if (!Schema::hasColumn('bookings', 'cashfree_payment_id')) {
                    $table->string('cashfree_payment_id')->nullable()->after('cashfree_order_id');
                }
            });
        }

        // 3. Update Payments Table
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'payment_gateway')) {
                    $table->string('payment_gateway', 30)->default('razorpay')->after('booking_id');
                }
                if (!Schema::hasColumn('payments', 'gateway_order_id')) {
                    $table->string('gateway_order_id')->nullable()->after('payment_gateway');
                }
                if (!Schema::hasColumn('payments', 'gateway_payment_id')) {
                    $table->string('gateway_payment_id')->nullable()->index()->after('gateway_order_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn(['payment_gateway', 'gateway_order_id', 'gateway_payment_id']);
            });
        }

        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn(['payment_gateway', 'cashfree_order_id', 'cashfree_payment_id']);
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn(['gateway', 'cashfree_order_id', 'cashfree_payment_id', 'payment_session_id']);
            });
        }
    }
};
