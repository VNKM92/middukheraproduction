<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Gallery;
use App\Models\Package;
use App\Models\Visitor;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    private function trackVisitor(Request $request)
    {
        try {
            $ip = $request->ip();
            $recent = Visitor::where('ip_address', $ip)
                ->where('visited_at', '>=', now()->subHour())
                ->exists();
            if (!$recent) {
                Visitor::create([
                    'ip_address' => $ip ?? '127.0.0.1',
                    'visited_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Silence visitor tracking errors in case of DB issues
        }
    }

    public function index(Request $request)
    {
        $this->trackVisitor($request);
        $packages = Package::all();
        $blogs = Blog::latest()->take(3)->get();
        $gallery = Gallery::latest()->take(8)->get();
        $testimonials = Testimonial::where('is_featured', true)->orderBy('order')->get();
        if ($testimonials->isEmpty()) {
            $testimonials = Testimonial::orderBy('order')->take(6)->get();
        }
        $faqs = Faq::where('is_active', true)->orderBy('order')->take(6)->get();
        $teamMembers = TeamMember::where('is_active', true)->orderBy('order')->take(4)->get();

        $meta_title = (config('app.name') ?: 'Middukhera Production') . ' — Luxury Photoshoot & Production House';
        $meta_description = 'Book premium photoshoot packages for weddings, fashion, portraits and events with Middukhera Production. Instant Razorpay booking.';
        $meta_image = $packages->first()->image_path ?? asset('favicon.ico');

        return view('frontend.home', compact('packages', 'blogs', 'gallery', 'testimonials', 'faqs', 'teamMembers', 'meta_title', 'meta_description', 'meta_image'));
    }

    public function about(Request $request)
    {
        $this->trackVisitor($request);
        $page = Page::where('slug', 'about')->first();
        $teamMembers = TeamMember::where('is_active', true)->orderBy('order')->get();
        $testimonials = Testimonial::where('is_featured', true)->orderBy('order')->take(3)->get();
        $faqs = Faq::where('is_active', true)->orderBy('order')->get();

        return view('frontend.about', compact('page', 'teamMembers', 'testimonials', 'faqs'));
    }

    public function gallery(Request $request)
    {
        $this->trackVisitor($request);
        $categories = Gallery::select('category')->distinct()->pluck('category');
        $galleryItems = Gallery::latest()->get();

        return view('frontend.gallery', compact('galleryItems', 'categories'));
    }

    public function blog(Request $request)
    {
        $this->trackVisitor($request);
        $blogs = Blog::latest()->paginate(6);
        $recentBlogs = Blog::latest()->take(4)->get();
        return view('frontend.blog', compact('blogs', 'recentBlogs'));
    }

    public function blogSingle($slug, Request $request)
    {
        $this->trackVisitor($request);
        $blog = Blog::where('slug', $slug)->firstOrFail();
        $recentBlogs = Blog::where('id', '!=', $blog->id)->latest()->take(3)->get();

        $meta_title = $blog->meta_title ?? $blog->title;
        $meta_description = $blog->meta_description ?? $blog->excerpt;
        $meta_image = $blog->image_path ?? asset('favicon.ico');

        return view('frontend.blog-single', compact('blog', 'recentBlogs', 'meta_title', 'meta_description', 'meta_image'));
    }

    public function contact(Request $request)
    {
        $this->trackVisitor($request);
        $faqs = Faq::where('is_active', true)->orderBy('order')->take(4)->get();
        return view('frontend.contact', compact('faqs'));
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        ContactMessage::create($request->all());

        return redirect()->back()->with('success', 'Thank you! Your message has been sent successfully. Our concierge will get back to you shortly.');
    }

    public function terms(Request $request)
    {
        $this->trackVisitor($request);
        $page = Page::where('slug', 'terms')->first();
        return view('frontend.terms', compact('page'));
    }

    public function privacy(Request $request)
    {
        $this->trackVisitor($request);
        $page = Page::where('slug', 'privacy')->first();
        return view('frontend.privacy', compact('page'));
    }

    public function refundPolicy(Request $request)
    {
        $this->trackVisitor($request);
        $page = Page::where('slug', 'refund-policy')->first();
        return view('frontend.refund-policy', compact('page'));
    }

    public function shippingPolicy(Request $request)
    {
        $this->trackVisitor($request);
        $page = Page::where('slug', 'shipping-policy')->first();
        return view('frontend.shipping-policy', compact('page'));
    }

    public function disclaimer(Request $request)
    {
        $this->trackVisitor($request);
        $page = Page::where('slug', 'disclaimer')->first();
        return view('frontend.disclaimer', compact('page'));
    }

    public function showCustomPage($slug, Request $request)
    {
        $this->trackVisitor($request);
        $page = Page::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return view('frontend.custom-page', compact('page'));
    }
}
