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
        // 1. Packages
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('price_min');
            $table->integer('price_max');
            $table->text('description');
            $table->json('features')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // 2. Bookings
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->date('booking_date');
            $table->string('status')->default('pending'); // pending, progress, active, next_level, completed, cancelled
            $table->string('payment_status')->default('pending'); // pending, completed, failed
            $table->integer('amount');
            $table->text('notes')->nullable();
            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            $table->timestamps();
        });

        // 3. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('razorpay_payment_id')->unique();
            $table->integer('amount');
            $table->string('status');
            $table->string('payment_method')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });

        // 4. Blogs
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('content');
            $table->string('image_path')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();
        });

        // 5. Gallery
        Schema::create('gallery', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // Wedding, Fashion, Editorial, Portrait, Event
            $table->string('image_path');
            $table->timestamps();
        });

        // 6. Contact Messages
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('unread'); // unread, read
            $table->timestamps();
        });

        // 7. Visitors
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->timestamp('visited_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('gallery');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('packages');
    }
};
