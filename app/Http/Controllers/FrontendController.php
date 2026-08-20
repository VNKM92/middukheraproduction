<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Gallery;
use App\Models\Package;
use App\Models\Visitor;
use App\Models\ContactMessage;
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
        $gallery = Gallery::latest()->take(6)->get();

        $meta_title = config('app.name') . ' — Premium Photoshoot Studio';
        $meta_description = 'Book premium photoshoot packages for weddings, fashion, portraits and events. Professional photographers, studios and styling.';
        $meta_image = $packages->first()->image_path ?? asset('favicon.ico');

        return view('frontend.home', compact('packages', 'blogs', 'gallery', 'meta_title', 'meta_description', 'meta_image'));
    }

    public function about(Request $request)
    {
        $this->trackVisitor($request);
        return view('frontend.about');
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
        return view('frontend.blog', compact('blogs'));
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
        return view('frontend.contact');
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

        return redirect()->back()->with('success', 'Thank you! Your message has been sent successfully. We will get back to you shortly.');
    }

    public function terms(Request $request)
    {
        $this->trackVisitor($request);
        return view('frontend.terms');
    }

    public function disclaimer(Request $request)
    {
        $this->trackVisitor($request);
        return view('frontend.disclaimer');
    }
}
