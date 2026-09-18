<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Visitor;
use App\Models\ContactMessage;
use App\Models\Blog;
use App\Models\Gallery;
use App\Models\Package;
use App\Models\Setting;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Transaction;
use App\Models\SmsLog;
use App\Models\WebhookLog;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\TeamMember;
use App\Models\MediaFile;
use App\Services\UploadService;
use App\Services\Sms\SmsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalEarnings = Transaction::where('status', 'captured')->sum('amount') ?: Payment::where('status', 'captured')->sum('amount');
        $totalBookings = Booking::where('payment_status', 'completed')->count();
        $totalPendingBookings = Booking::where('payment_status', '!=', 'completed')->count();
        $totalVisitors = Visitor::count();
        $totalPackages = Package::count();
        $totalVendors = Vendor::count();
        $unreadMessagesCount = ContactMessage::where('status', 'unread')->count();
        
        $bookings = Booking::with(['user', 'package'])->latest()->get();
        $payments = Payment::with('booking.user')->latest()->take(25)->get();
        $transactions = Transaction::with(['booking.package', 'user'])->latest()->take(100)->get();
        $smsLogs = SmsLog::latest()->take(50)->get();
        $webhookLogs = WebhookLog::latest()->take(50)->get();

        $messages = ContactMessage::latest()->get();
        $blogs = Blog::latest()->get();
        $gallery = Gallery::latest()->get();
        $packages = Package::with('vendor')->latest()->get();
        $vendors = Vendor::with('user')->latest()->get();
        $users = User::latest()->take(20)->get();

        // Dynamic CMS & Media Content
        $pages = Page::latest()->get();
        $testimonials = Testimonial::orderBy('order')->latest()->get();
        $faqs = Faq::orderBy('order')->get();
        $teamMembers = TeamMember::orderBy('order')->get();
        $mediaFiles = MediaFile::latest()->get();

        $allSettings = Setting::getAllAsArray();

        return view('admin.dashboard', compact(
            'totalEarnings',
            'totalBookings',
            'totalPendingBookings',
            'totalVisitors',
            'totalPackages',
            'totalVendors',
            'unreadMessagesCount',
            'bookings',
            'payments',
            'transactions',
            'smsLogs',
            'webhookLogs',
            'messages',
            'blogs',
            'gallery',
            'packages',
            'vendors',
            'users',
            'pages',
            'testimonials',
            'faqs',
            'teamMembers',
            'mediaFiles',
            'allSettings'
        ));
    }

    /**
     * Update Site & Theme Settings with File Upload Support
     */
    public function saveSettings(Request $request)
    {
        $data = $request->except(['_token', 'theme_preset', 'site_logo_file', 'site_favicon_file', 'hero_bg_image_file', 'about_banner_file']);

        // Handle File Uploads for branding and banners
        if ($request->hasFile('site_logo_file')) {
            $upload = UploadService::upload($request->file('site_logo_file'), 'settings');
            $data['site_logo'] = $upload['url'];
        }

        if ($request->hasFile('site_favicon_file')) {
            $upload = UploadService::upload($request->file('site_favicon_file'), 'settings');
            $data['site_favicon'] = $upload['url'];
        }

        if ($request->hasFile('hero_bg_image_file')) {
            $upload = UploadService::upload($request->file('hero_bg_image_file'), 'settings');
            $data['hero_bg_image'] = $upload['url'];
        }

        if ($request->hasFile('about_banner_file')) {
            $upload = UploadService::upload($request->file('about_banner_file'), 'settings');
            $data['about_banner_image'] = $upload['url'];
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Setting::clearCache();

        return redirect()->back()->with('success', 'Site settings, media assets & theme configuration updated successfully!');
    }

    /**
     * Test SMS dispatch from admin settings
     */
    public function testSms(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string|min:8|max:20',
            'test_message' => 'required|string|max:500',
            'test_driver' => 'nullable|string|in:auto,twilio,fast2sms,msg91,custom_http,simulation',
        ]);

        $testDriver = $request->input('test_driver');

        $result = SmsManager::dispatch(
            phone: $request->test_phone,
            message: $request->test_message,
            templateKey: 'admin_test',
            overrideDriver: $testDriver
        );

        if ($result['success']) {
            $driverUsed = $result['driver_used'] ?? ($testDriver ?: Setting::get('sms_driver', 'auto'));
            return redirect()->back()->with('success', "Test SMS successfully dispatched via [{$driverUsed}]! Result: " . ($result['message'] ?? 'Dispatched.'));
        }

        return redirect()->back()->with('error', 'Test SMS failed: ' . ($result['message'] ?? 'Unknown error occurred. Please verify your API credentials.'));
    }

    /**
     * Apply Preset Theme
     */
    public function applyThemePreset(Request $request)
    {
        $preset = $request->input('preset', 'luxury_gold');

        $presets = [
            'luxury_gold' => [
                'primary_color' => '#E5C158',
                'primary_hover' => '#F3D88B',
                'secondary_color' => '#B8922E',
                'accent_color' => '#8B5CF6',
                'bg_color' => '#07060a',
                'card_bg_color' => '#12101b',
                'text_color' => '#F3F4F6',
                'border_color' => '#272438',
            ],
            'obsidian_neon' => [
                'primary_color' => '#00F0FF',
                'primary_hover' => '#70F7FF',
                'secondary_color' => '#0098A6',
                'accent_color' => '#FF0055',
                'bg_color' => '#05070c',
                'card_bg_color' => '#0e121a',
                'text_color' => '#F3F4F6',
                'border_color' => '#1c2436',
            ],
            'royal_emerald' => [
                'primary_color' => '#10B981',
                'primary_hover' => '#34D399',
                'secondary_color' => '#059669',
                'accent_color' => '#F59E0B',
                'bg_color' => '#040d0a',
                'card_bg_color' => '#081c15',
                'text_color' => '#F3F4F6',
                'border_color' => '#13392a',
            ],
            'rose_champagne' => [
                'primary_color' => '#F472B6',
                'primary_hover' => '#FBCFE8',
                'secondary_color' => '#DB2777',
                'accent_color' => '#FBBF24',
                'bg_color' => '#0c0709',
                'card_bg_color' => '#1a0e14',
                'text_color' => '#FDF2F8',
                'border_color' => '#3b1c2b',
            ],
            'cyberpunk_violet' => [
                'primary_color' => '#A855F7',
                'primary_hover' => '#C084FC',
                'secondary_color' => '#7E22CE',
                'accent_color' => '#EC4899',
                'bg_color' => '#090510',
                'card_bg_color' => '#140c24',
                'text_color' => '#F5F3FF',
                'border_color' => '#2a1a47',
            ],
            'minimal_light' => [
                'primary_color' => '#18181B',
                'primary_hover' => '#3F3F46',
                'secondary_color' => '#71717A',
                'accent_color' => '#6366F1',
                'bg_color' => '#F8FAFC',
                'card_bg_color' => '#FFFFFF',
                'text_color' => '#0F172A',
                'border_color' => '#E2E8F0',
            ],
        ];

        if (isset($presets[$preset])) {
            foreach ($presets[$preset] as $key => $val) {
                Setting::set($key, $val, 'theme');
            }
            Setting::clearCache();
            return redirect()->back()->with('success', 'Theme preset applied successfully!');
        }

        return redirect()->back()->with('error', 'Preset not recognized.');
    }

    // ==========================================
    // 1. MEDIA LIBRARY & UPLOAD CENTER (ADVANCE)
    // ==========================================

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'folder' => 'nullable|string|max:50',
            'file' => 'nullable|file|max:20480',
            'files.*' => 'nullable|file|max:20480',
        ]);

        $folder = $request->input('folder', 'general');
        $uploadedCount = 0;

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                UploadService::upload($file, $folder);
                $uploadedCount++;
            }
        } elseif ($request->hasFile('file')) {
            UploadService::upload($request->file('file'), $folder);
            $uploadedCount++;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$uploadedCount} file(s) uploaded successfully.",
            ]);
        }

        return redirect()->back()->with('success', "{$uploadedCount} media file(s) uploaded to {$folder} library successfully.");
    }

    public function deleteMedia(MediaFile $media)
    {
        UploadService::delete($media);
        return redirect()->back()->with('success', 'Media file permanently deleted.');
    }

    // ==========================================
    // 2. DYNAMIC PAGES & POLICIES CRUD
    // ==========================================

    public function storePage(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'banner_image_url' => 'nullable|url',
            'banner_image' => 'nullable|image|max:10240',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $bannerPath = $request->banner_image_url;
        if ($request->hasFile('banner_image')) {
            $upload = UploadService::upload($request->file('banner_image'), 'pages');
            $bannerPath = $upload['url'];
        }

        Page::create([
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'subtitle' => $request->subtitle,
            'content' => $request->content,
            'banner_image' => $bannerPath,
            'meta_title' => $request->meta_title ?? $request->title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->back()->with('success', "Page [{$request->title}] created successfully.");
    }

    public function updatePage(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'banner_image_url' => 'nullable|url',
            'banner_image' => 'nullable|image|max:10240',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $bannerPath = $page->banner_image;
        if ($request->hasFile('banner_image')) {
            $upload = UploadService::upload($request->file('banner_image'), 'pages');
            $bannerPath = $upload['url'];
        } elseif ($request->filled('banner_image_url')) {
            $bannerPath = $request->banner_image_url;
        }

        $page->update([
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'subtitle' => $request->subtitle,
            'content' => $request->content,
            'banner_image' => $bannerPath,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->back()->with('success', "Page [{$page->title}] updated successfully.");
    }

    public function deletePage(Page $page)
    {
        $title = $page->title;
        $page->delete();
        return redirect()->back()->with('success', "Page [{$title}] deleted successfully.");
    }

    // ==========================================
    // 3. TESTIMONIALS & REVIEWS CRUD
    // ==========================================

    public function storeTestimonial(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_role' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|url',
            'avatar' => 'nullable|image|max:5120',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string',
            'event_type' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $avatarPath = $request->avatar_url;
        if ($request->hasFile('avatar')) {
            $upload = UploadService::upload($request->file('avatar'), 'testimonials');
            $avatarPath = $upload['url'];
        }

        Testimonial::create([
            'client_name' => $request->client_name,
            'client_role' => $request->client_role,
            'avatar_path' => $avatarPath ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300',
            'rating' => $request->rating,
            'content' => $request->content,
            'event_type' => $request->event_type,
            'is_featured' => $request->boolean('is_featured', true),
            'order' => $request->input('order', 0),
        ]);

        return redirect()->back()->with('success', 'Testimonial added successfully.');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_role' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|url',
            'avatar' => 'nullable|image|max:5120',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string',
            'event_type' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $avatarPath = $testimonial->avatar_path;
        if ($request->hasFile('avatar')) {
            $upload = UploadService::upload($request->file('avatar'), 'testimonials');
            $avatarPath = $upload['url'];
        } elseif ($request->filled('avatar_url')) {
            $avatarPath = $request->avatar_url;
        }

        $testimonial->update([
            'client_name' => $request->client_name,
            'client_role' => $request->client_role,
            'avatar_path' => $avatarPath,
            'rating' => $request->rating,
            'content' => $request->content,
            'event_type' => $request->event_type,
            'is_featured' => $request->boolean('is_featured', true),
            'order' => $request->input('order', 0),
        ]);

        return redirect()->back()->with('success', 'Testimonial updated successfully.');
    }

    public function deleteTestimonial(Testimonial $testimonial)
    {
        $testimonial->delete();
        return redirect()->back()->with('success', 'Testimonial deleted successfully.');
    }

    // ==========================================
    // 4. FAQS (FREQUENTLY ASKED QUESTIONS) CRUD
    // ==========================================

    public function storeFaq(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|string|max:50',
            'order' => 'nullable|integer',
        ]);

        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'order' => $request->input('order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', 'FAQ added successfully.');
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|string|max:50',
            'order' => 'nullable|integer',
        ]);

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'order' => $request->input('order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', 'FAQ updated successfully.');
    }

    public function deleteFaq(Faq $faq)
    {
        $faq->delete();
        return redirect()->back()->with('success', 'FAQ deleted successfully.');
    }

    // ==========================================
    // 5. TEAM MEMBERS & CREW CRUD
    // ==========================================

    public function storeTeamMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|max:5120',
            'instagram_url' => 'nullable|url',
            'order' => 'nullable|integer',
        ]);

        $imagePath = $request->image_url;
        if ($request->hasFile('image')) {
            $upload = UploadService::upload($request->file('image'), 'team');
            $imagePath = $upload['url'];
        }

        TeamMember::create([
            'name' => $request->name,
            'role' => $request->role,
            'bio' => $request->bio,
            'image_path' => $imagePath ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400',
            'instagram_url' => $request->instagram_url,
            'order' => $request->input('order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', 'Team member added successfully.');
    }

    public function updateTeamMember(Request $request, TeamMember $teamMember)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|max:5120',
            'instagram_url' => 'nullable|url',
            'order' => 'nullable|integer',
        ]);

        $imagePath = $teamMember->image_path;
        if ($request->hasFile('image')) {
            $upload = UploadService::upload($request->file('image'), 'team');
            $imagePath = $upload['url'];
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $teamMember->update([
            'name' => $request->name,
            'role' => $request->role,
            'bio' => $request->bio,
            'image_path' => $imagePath,
            'instagram_url' => $request->instagram_url,
            'order' => $request->input('order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', 'Team member updated successfully.');
    }

    public function deleteTeamMember(TeamMember $teamMember)
    {
        $teamMember->delete();
        return redirect()->back()->with('success', 'Team member deleted successfully.');
    }

    // ==========================================
    // 6. ENHANCED BLOG / JOURNAL CRUD
    // ==========================================

    public function storeBlog(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|max:10240',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $imagePath = $request->image_url ?? null;
        if ($request->hasFile('image')) {
            $upload = UploadService::upload($request->file('image'), 'blogs');
            $imagePath = $upload['url'];
        }

        Blog::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . uniqid(),
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'image_path' => $imagePath ?? 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=800',
            'meta_title' => $request->meta_title ?? ($request->title . ' | Studio Journal'),
            'meta_description' => $request->meta_description ?? $request->excerpt,
            'meta_keywords' => $request->meta_keywords ?? 'photoshoot, studio, photography, portfolio',
        ]);

        return redirect()->back()->with('success', 'Blog article published successfully.');
    }

    public function updateBlog(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|max:10240',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $imagePath = $blog->image_path;
        if ($request->hasFile('image')) {
            $upload = UploadService::upload($request->file('image'), 'blogs');
            $imagePath = $upload['url'];
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $blog->update([
            'title' => $request->title,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'image_path' => $imagePath,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ]);

        return redirect()->back()->with('success', 'Blog article updated successfully.');
    }

    public function deleteBlog(Blog $blog)
    {
        $blog->delete();
        return redirect()->back()->with('success', 'Blog article deleted successfully.');
    }

    // ==========================================
    // 7. ENHANCED GALLERY CRUD
    // ==========================================

    public function storeGallery(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|max:10240',
        ]);

        $imagePath = $request->image_url ?? null;
        if ($request->hasFile('image')) {
            $upload = UploadService::upload($request->file('image'), 'gallery');
            $imagePath = $upload['url'];
        }

        if (!$imagePath) {
            $imagePath = 'https://images.unsplash.com/photo-1519741497674-611481863552?w=800';
        }

        Gallery::create([
            'title' => $request->title,
            'category' => $request->category,
            'image_path' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Gallery item uploaded successfully.');
    }

    public function updateGallery(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|max:10240',
        ]);

        $imagePath = $gallery->image_path;
        if ($request->hasFile('image')) {
            $upload = UploadService::upload($request->file('image'), 'gallery');
            $imagePath = $upload['url'];
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $gallery->update([
            'title' => $request->title,
            'category' => $request->category,
            'image_path' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Gallery item updated successfully.');
    }

    public function deleteGallery(Gallery $gallery)
    {
        $gallery->delete();
        return redirect()->back()->with('success', 'Gallery item removed successfully.');
    }

    // ==========================================
    // 8. PRICING PACKAGES CRUD
    // ==========================================

    public function storePackage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price_min' => 'required|numeric|min:0',
            'price_max' => 'required|numeric|gte:price_min',
            'description' => 'required|string',
            'features' => 'required|string',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|max:10240',
        ]);

        $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $request->features))));

        $imagePath = $request->image_url ?? null;
        if ($request->hasFile('image')) {
            $upload = UploadService::upload($request->file('image'), 'packages');
            $imagePath = $upload['url'];
        }

        Package::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'description' => $request->description,
            'features' => $featuresArray,
            'image_path' => $imagePath ?? 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=800',
        ]);

        return redirect()->back()->with('success', 'Pricing package created successfully.');
    }

    public function updatePackage(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price_min' => 'required|numeric|min:0',
            'price_max' => 'required|numeric|gte:price_min',
            'description' => 'required|string',
            'features' => 'required|string',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|max:10240',
        ]);

        $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $request->features))));

        $imagePath = $package->image_path;
        if ($request->hasFile('image')) {
            $upload = UploadService::upload($request->file('image'), 'packages');
            $imagePath = $upload['url'];
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $package->update([
            'name' => $request->name,
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'description' => $request->description,
            'features' => $featuresArray,
            'image_path' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Pricing package updated successfully.');
    }

    public function deletePackage(Package $package)
    {
        $package->delete();
        return redirect()->back()->with('success', 'Package deleted successfully.');
    }

    // ==========================================
    // 9. BOOKINGS & INQUIRIES & VENDORS
    // ==========================================

    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,progress,active,next_level,completed,cancelled',
        ]);

        $booking->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Booking status updated to ' . ucfirst($request->status) . ' successfully.');
    }

    public function deleteBooking(Booking $booking)
    {
        $booking->delete();
        return redirect()->back()->with('success', 'Booking record deleted successfully.');
    }

    public function updateVendorStatus(Request $request, Vendor $vendor)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,suspended',
        ]);

        $vendor->update(['status' => $request->status]);

        if ($request->status === 'approved' && $vendor->user) {
            $vendor->user->update(['role' => 'vendor']);
        }

        return redirect()->back()->with('success', 'Vendor status updated to ' . ucfirst($request->status));
    }

    public function markMessageRead(ContactMessage $message)
    {
        $message->update(['status' => 'read']);
        return redirect()->back()->with('success', 'Message marked as read.');
    }

    public function vendorsList()
    {
        $vendors = Vendor::with('user')->latest()->get();
        return view('admin.vendors', compact('vendors'));
    }

    public function deleteMessage(ContactMessage $message)
    {
        $message->delete();
        return redirect()->back()->with('success', 'Message deleted.');
    }
}
