<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Gallery;
use App\Models\Package;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Dynamic Theme & System Settings
        $defaultSettings = [
            'site_name' => 'Lumina Studio & Productions',
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
            'border_color' => 'rgba(255, 255, 255, 0.08)',
            'currency_symbol' => '₹',
            'contact_email' => 'concierge@luminastudio.com',
            'contact_phone' => '+91 98765 43210',
            'contact_address' => 'Suite 402, Signature Art Tower, Fashion Boulevard, Mumbai, MH 400050',
            'operating_hours' => 'Mon - Sun: 09:00 AM - 09:00 PM IST',
            'social_instagram' => 'https://instagram.com',
            'social_facebook' => 'https://facebook.com',
            'social_youtube' => 'https://youtube.com',
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
            'razorpay_key_id' => 'rzp_test_sample',
            'razorpay_simulation_mode' => '1',
        ];

        foreach ($defaultSettings as $key => $val) {
            Setting::set($key, $val);
        }

        // 2. Create admin and client users
        $adminData = [
            'name' => 'Super Admin',
            'email' => 'admin@studio.test',
            'password' => 'password',
        ];

        $clientData = [
            'name' => 'Client User',
            'email' => 'client@studio.test',
            'password' => 'password',
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'role')) {
            $adminData['role'] = 'super_admin';
            $clientData['role'] = 'client';
        }

        if (!User::where('email', $adminData['email'])->exists()) {
            User::factory()->create($adminData);
        }

        if (!User::where('email', $clientData['email'])->exists()) {
            User::factory()->create($clientData);
        }

        // 3. Seed 6 signature packages
        $packageData = [
            [
                'name' => 'Signature Portraiture',
                'price_min' => 15000,
                'price_max' => 35000,
                'description' => 'Fine art solo portrait sessions in our state-of-the-art studio. Ideal for professional branding, editorial modeling portfolios, and personal legacy imagery.',
                'features' => json_encode(["90-minute private studio session", "Professional hair & makeup stylist", "20 high-fidelity edited digital plates", "1 signature museum-grade print (12x18)"]),
                'image_path' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'name' => 'Haute Couture Editorial',
                'price_min' => 45000,
                'price_max' => 85000,
                'description' => 'Avante-garde fashion editorial photography tailored for designers, agencies, and high-end campaigns. Full creative direction, custom lighting arrangements, and dramatic moodboards.',
                'features' => json_encode(["3-hour studio & outdoor shoot", "Senior creative director support", "Full stylist wardrobe access", "40 editorial retouched images", "Feature submission support for digital journals"]),
                'image_path' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'name' => 'Eternal Love (Wedding)',
                'price_min' => 120000,
                'price_max' => 250000,
                'description' => 'Cinematic wedding coverage documenting your unique story. Immersive emotional documentation, candid artistic captures, and high-end cinematic post-production.',
                'features' => json_encode(["Full-day coverage (2 senior photographers)", "Cinematic 4K teaser & highlight reels", "150+ masterfully color-graded plates", "Luxury handmade leather-bound layflat album", "Complimentary pre-wedding shoot"]),
                'image_path' => 'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'name' => 'Prestige Corporate Branding',
                'price_min' => 60000,
                'price_max' => 110000,
                'description' => 'Premium brand identity visual packs for executives, modern corporations, and fast-growing teams. Clean, powerful corporate portraiture and dynamic lifestyle workspace captures.',
                'features' => json_encode(["Half-day shoot on-location or in-studio", "Full team headshots & group layout", "15 branding lifestyle action plates", "Commercial licensing included", "Express 48-hour master delivery"]),
                'image_path' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'name' => 'Avant-Garde Architectural',
                'price_min' => 75000,
                'price_max' => 150000,
                'description' => 'High-end interior, architecture, and luxury real estate photography. Capturing structural geometries, spatial flow, and ambient lighting with professional tilt-shift optics.',
                'features' => json_encode(["Day & dusk dual lighting capture", "Styling & spatial arrangement setup", "30 high-resolution architectural plates", "Advanced HDR exposure blending", "Editorial rights for publishing"]),
                'image_path' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'name' => 'Elite Creative Campaign',
                'price_min' => 200000,
                'price_max' => 450000,
                'description' => 'The ultimate custom campaign solution. Custom production design, location scouting, set construction, models, styling, and premium cinematic styling tailored to international standards.',
                'features' => json_encode(["Multi-day customized production", "Full production crew & set builders", "Advanced 100MP camera equipment", "Raw format capture & unlimited edits", "Worldwide commercial usage rights"]),
                'image_path' => 'https://images.unsplash.com/photo-1493863641943-9b68992a8d07?q=80&w=1200&auto=format&fit=crop',
            ]
        ];

        $createdPackages = [];
        foreach ($packageData as $pkgInfo) {
            $slug = Str::slug($pkgInfo['name']);
            $pkg = Package::updateOrCreate([
                'slug' => $slug,
            ], [
                'name' => $pkgInfo['name'],
                'price_min' => $pkgInfo['price_min'],
                'price_max' => $pkgInfo['price_max'],
                'description' => $pkgInfo['description'],
                'features' => $pkgInfo['features'],
                'image_path' => $pkgInfo['image_path'],
            ]);
            $createdPackages[] = $pkg;
        }

        // 4. Create vendor user and vendor record
        if (!User::where('email', 'vendor@studio.test')->exists()) {
            $vendorUser = User::factory()->create([
                'name' => 'Lumina Master Artist',
                'email' => 'vendor@studio.test',
                'role' => 'vendor',
                'password' => 'password',
            ]);
        } else {
            $vendorUser = User::where('email', 'vendor@studio.test')->first();
        }

        $vendor = \App\Models\Vendor::firstOrCreate([
            'slug' => Str::slug('lumina-master-artist'),
        ], [
            'user_id' => $vendorUser->id,
            'name' => 'Lumina Master Artist',
            'description' => 'Lumina Studio’s elite photographer with over a decade of high-fashion and wedding editorial experience.',
            'status' => 'approved',
        ]);

        if (\Illuminate\Support\Facades\Schema::hasColumn('packages', 'vendor_id')) {
            for ($i = 0; $i < 2 && isset($createdPackages[$i]); $i++) {
                $createdPackages[$i]->update(['vendor_id' => $vendor->id]);
            }
        }

        // 5. Seed blog masterclasses
        $blogData = [
            [
                'title' => 'Mastering Light: The Art of Studio Portraiture',
                'excerpt' => 'An insider look into carving details, defining moods, and utilizing high-contrast shadows to capture soul-stirring portraits.',
                'content' => '<p>Lighting is the language of photography. In this editorial, our principal photographers break down the specific setups—such as Rembrant lighting, broad vs. narrow keying, and utilizing giant silver parasols—to create portraits that command attention.</p>',
                'image_path' => 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'title' => 'The Bridal Silhouette: Documenting Eternal Stories',
                'excerpt' => 'Capturing the raw emotion, delicate silk textures, and quiet pre-ceremony moments that make wedding photography timeless.',
                'content' => '<p>A wedding is not a photoshoot; it is a live cinematic documentary. In this guide, we dive into how to capture genuine emotional reactions with natural light and fluid storytelling.</p>',
                'image_path' => 'https://images.unsplash.com/photo-1532712938310-34cb3982ef74?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'title' => 'Behind the Scenes of a High-Fashion Editorial',
                'excerpt' => 'Exploring the creative alignment of models, designers, stylists, and custom lighting designs to execute high-impact imagery.',
                'content' => '<p>Fashion photography is a collaborative symphony. This article walks you through the lifecycle of an editorial campaign from moodboarding to final glossy magazine publication.</p>',
                'image_path' => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?q=80&w=1200&auto=format&fit=crop',
            ]
        ];

        foreach ($blogData as $bInfo) {
            $slug = Str::slug($bInfo['title']);
            Blog::updateOrCreate([
                'slug' => $slug,
            ], [
                'title' => $bInfo['title'],
                'excerpt' => $bInfo['excerpt'],
                'content' => $bInfo['content'],
                'image_path' => $bInfo['image_path'],
                'meta_title' => $bInfo['title'] . ' | Lumina Stories',
                'meta_description' => $bInfo['excerpt'],
                'meta_keywords' => 'photoshoot, masterclass, luxury photography, how-to',
            ]);
        }

        // 6. Seed gallery items
        $galleryData = [
            ['title' => 'Celestial Radiance', 'category' => 'Wedding', 'img' => 'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=800&auto=format&fit=crop'],
            ['title' => 'Chamber of Shadows', 'category' => 'Portrait', 'img' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=800&auto=format&fit=crop'],
            ['title' => 'Gilded Silk', 'category' => 'Fashion', 'img' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=800&auto=format&fit=crop'],
            ['title' => 'Metropolitan Angle', 'category' => 'Editorial', 'img' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?q=80&w=800&auto=format&fit=crop'],
            ['title' => 'Verve Gala', 'category' => 'Event', 'img' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=800&auto=format&fit=crop'],
            ['title' => 'Geometric Balance', 'category' => 'Product', 'img' => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?q=80&w=800&auto=format&fit=crop'],
        ];

        foreach ($galleryData as $item) {
            Gallery::updateOrCreate([
                'title' => $item['title'],
            ], [
                'category' => $item['category'],
                'image_path' => $item['img'],
            ]);
        }

        $this->command->info('Seeded dynamic settings, luxury packages, users, blogs and gallery showcase successfully.');
    }
}
