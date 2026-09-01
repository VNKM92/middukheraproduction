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

        $allSettings = Setting::getAllAsArray();

        return view('admin.dashboard', compact(
            'totalEarnings',
            'totalBookings',
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
            'allSettings'
        ));
    }

    /**
     * Update Site & Theme Settings
     */
    public function saveSettings(Request $request)
    {
        $data = $request->except(['_token', 'theme_preset']);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Setting::clearCache();

        return redirect()->back()->with('success', 'Site settings & theme colors saved successfully!');
    }

    /**
     * Test SMS dispatch from admin settings
     */
    public function testSms(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string|min:8|max:20',
            'test_message' => 'required|string|max:500',
        ]);

        $result = SmsManager::dispatch(
            phone: $request->test_phone,
            message: $request->test_message,
            templateKey: 'admin_test'
        );

        if ($result['success']) {
            return redirect()->back()->with('success', 'Test SMS successfully dispatched! Check SMS logs below or check recipient device. ' . ($result['message'] ?? ''));
        }

        return redirect()->back()->with('error', 'Test SMS failed: ' . ($result['message'] ?? 'Unknown error occurred.'));
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

    public function storeBlog(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        Blog::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . uniqid(),
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'image_path' => $request->image_url ?? 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=800',
            'meta_title' => $request->title . ' | Lumina Studio',
            'meta_description' => $request->excerpt,
            'meta_keywords' => 'photoshoot, studio, photography, portfolio',
        ]);

        return redirect()->back()->with('success', 'Blog article published successfully.');
    }

    public function deleteBlog(Blog $blog)
    {
        $blog->delete();
        return redirect()->back()->with('success', 'Blog article deleted successfully.');
    }

    public function storeGallery(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|max:5120',
        ]);

        $imagePath = $request->image_url ?? null;
        if ($request->hasFile('image')) {
            $stored = $request->file('image')->store('uploads', 'public');
            $imagePath = \Illuminate\Support\Facades\Storage::url($stored);
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

    public function deleteGallery(Gallery $gallery)
    {
        $gallery->delete();
        return redirect()->back()->with('success', 'Gallery item removed successfully.');
    }

    public function storePackage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price_min' => 'required|numeric|min:0',
            'price_max' => 'required|numeric|gte:price_min',
            'description' => 'required|string',
            'features' => 'required|string', // comma or newline separated
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|max:5120',
        ]);

        $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $request->features))));

        $imagePath = $request->image_url ?? null;
        if ($request->hasFile('image')) {
            $stored = $request->file('image')->store('uploads', 'public');
            $imagePath = \Illuminate\Support\Facades\Storage::url($stored);
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
        ]);

        $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $request->features))));

        $package->update([
            'name' => $request->name,
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'description' => $request->description,
            'features' => $featuresArray,
            'image_path' => $request->image_url ?: $package->image_path,
        ]);

        return redirect()->back()->with('success', 'Pricing package updated successfully.');
    }

    public function deletePackage(Package $package)
    {
        $package->delete();
        return redirect()->back()->with('success', 'Package deleted successfully.');
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

    public function deleteMessage(ContactMessage $message)
    {
        $message->delete();
        return redirect()->back()->with('success', 'Message deleted.');
    }
}
