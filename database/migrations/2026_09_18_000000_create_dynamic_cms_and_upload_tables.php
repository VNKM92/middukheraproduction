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
        // 1. Dynamic Pages
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('subtitle')->nullable();
                $table->longText('content')->nullable();
                $table->string('banner_image')->nullable();
                $table->json('sections')->nullable(); // For flexible section blocks (e.g., pillars, milestones, vision)
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        // 2. Testimonials / Client Reviews
        if (!Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('client_name');
                $table->string('client_role')->nullable(); // e.g. "Bride & Groom", "Fashion Model", "Creative Director"
                $table->string('avatar_path')->nullable();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->text('content');
                $table->string('event_type')->nullable(); // e.g. "Royal Wedding", "Couture Editorial"
                $table->boolean('is_featured')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        // 3. FAQs (Frequently Asked Questions)
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->string('category')->default('general'); // general, booking, pricing, delivery
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Team Members / Photographers & Crew
        if (!Schema::hasTable('team_members')) {
            Schema::create('team_members', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('role'); // e.g. "Lead Cinematographer", "Principal Portraitist"
                $table->string('image_path')->nullable();
                $table->text('bio')->nullable();
                $table->string('instagram_url')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 5. Media Files / Upload Center Library
        if (!Schema::hasTable('media_files')) {
            Schema::create('media_files', function (Blueprint $table) {
                $table->id();
                $table->string('filename');
                $table->string('original_name');
                $table->string('path');
                $table->string('url');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size')->default(0); // bytes
                $table->string('folder')->default('general'); // general, packages, blogs, gallery, pages, settings
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('pages');
    }
};
