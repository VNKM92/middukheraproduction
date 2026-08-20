<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share site settings globally with all views
        View::composer('*', function ($view) {
            $defaultSettings = [
                'site_name' => config('app.name', 'Lumina Studio & Productions'),
                'site_tagline' => 'Capturing Eternal Elegance & High-Fashion Artistry',
                'logo_text' => 'LUMINA',
                'logo_sub' => 'STUDIO',
                'primary_color' => '#E5C158',
                'primary_hover' => '#F3D88B',
                'secondary_color' => '#B8922E',
                'accent_color' => '#8B5CF6',
                'bg_color' => '#07060a',
                'card_bg_color' => '#12101b',
                'text_color' => '#F3F4F6',
                'text_muted' => '#9CA3AF',
                'border_color' => '#272438',
                'currency_symbol' => '₹',
                'contact_email' => 'concierge@luminastudio.com',
                'contact_phone' => '+91 98765 43210',
                'contact_address' => 'Suite 402, Signature Art Tower, Fashion Boulevard, Mumbai, MH 400050',
                'operating_hours' => 'Mon - Sun: 09:00 AM - 09:00 PM IST',
                'google_map_embed' => 'https://maps.google.com/maps?q=Mumbai&t=&z=13&ie=UTF8&iwloc=&output=embed',
                'social_instagram' => 'https://instagram.com',
                'social_facebook' => 'https://facebook.com',
                'social_youtube' => 'https://youtube.com',
                'social_twitter' => 'https://twitter.com',
                'social_whatsapp' => '919876543210',
                'hero_badge' => '✨ INDIA’S PREMIER LUXURY PRODUCTION HOUSE',
                'hero_title' => 'Transforming Ephemeral Moments Into Timeless High-Art Masterpieces',
                'hero_subtitle' => 'Bespoke couture portraiture, celebrity fashion editorials, and cinematic wedding archives captured with world-class medium-format clarity.',
                'hero_bg_image' => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?q=80&w=1920&auto=format&fit=crop',
                'hero_cta_text' => 'Explore Packages & Reserve',
                'hero_secondary_cta' => 'View Master Portfolio',
                'stat_experience' => '12+ Years',
                'stat_experience_label' => 'Creative Mastery',
                'stat_shoots' => '2,800+',
                'stat_shoots_label' => 'Sessions Captured',
                'stat_awards' => '42+',
                'stat_awards_label' => 'Global Accolades',
                'stat_rating' => '4.98',
                'stat_rating_label' => 'Client Delight Score',
                'meta_title' => 'Lumina Studio | Premier Luxury Photoshoot & Production House',
                'meta_description' => 'Experience India\'s finest photoshoot studio. Luxury wedding cinematography, high-fashion editorials, signature portraits, and commercial campaigns with instant Razorpay booking.',
                'meta_keywords' => 'photoshoot studio, luxury photography, wedding photoshoot, fashion editorial, portrait studio, Mumbai photography, Razorpay studio booking',
                'razorpay_key_id' => env('RAZORPAY_KEY_ID', 'rzp_test_sample'),
                'razorpay_simulation_mode' => '1',
            ];

            try {
                if (Schema::hasTable('settings')) {
                    $dbSettings = Setting::getAllAsArray();
                    $settings = array_merge($defaultSettings, array_filter($dbSettings, fn($v) => !is_null($v) && $v !== ''));
                } else {
                    $settings = $defaultSettings;
                }
            } catch (\Exception $e) {
                $settings = $defaultSettings;
            }

            $view->with('siteSettings', $settings);
        });
    }
}
