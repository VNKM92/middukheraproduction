<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Package;
use App\Models\Gallery;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function index(Request $request)
    {
        // Cache sitemap for 30 minutes
        $xml = Cache::remember('sitemap_xml', 1800, function () {
            $urls = [];

            $now = Carbon::now()->toAtomString();

            // Static pages including all compliance policies
            $static = [
                ['', 'daily', '1.0'],
                ['about', 'monthly', '0.8'],
                ['gallery', 'weekly', '0.8'],
                ['blog', 'daily', '0.9'],
                ['contact', 'monthly', '0.6'],
                ['terms', 'monthly', '0.4'],
                ['privacy', 'monthly', '0.4'],
                ['refund-policy', 'monthly', '0.4'],
                ['shipping-policy', 'monthly', '0.4'],
                ['disclaimer', 'monthly', '0.3'],
            ];

            foreach ($static as $s) {
                $urls[] = [
                    'loc' => URL::to($s[0] ?: '/'),
                    'lastmod' => $now,
                    'changefreq' => $s[1],
                    'priority' => $s[2],
                ];
            }

            // Packages
            foreach (Package::all() as $p) {
                $urls[] = [
                    'loc' => URL::to('/package/' . $p->slug . '/checkout'),
                    'lastmod' => $p->updated_at ? $p->updated_at->toAtomString() : $now,
                    'changefreq' => 'weekly',
                    'priority' => '0.9',
                ];
            }

            // Blogs
            foreach (Blog::all() as $b) {
                $urls[] = [
                    'loc' => URL::to('/blog/' . $b->slug),
                    'lastmod' => $b->updated_at ? $b->updated_at->toAtomString() : $now,
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }

            // Gallery items
            foreach (Gallery::all() as $g) {
                $urls[] = [
                    'loc' => URL::to('/gallery'),
                    'lastmod' => $g->updated_at ? $g->updated_at->toAtomString() : $now,
                    'changefreq' => 'monthly',
                    'priority' => '0.5',
                ];
            }

            // Vendors (public profiles if implemented)
            foreach (Vendor::where('status','approved')->get() as $v) {
                $urls[] = [
                    'loc' => URL::to('/vendor/' . $v->slug),
                    'lastmod' => $v->updated_at ? $v->updated_at->toAtomString() : $now,
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            }

            $xml = view('sitemap.xml', ['urls' => $urls])->render();
            return $xml;
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
