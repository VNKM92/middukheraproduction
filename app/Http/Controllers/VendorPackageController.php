<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorPackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!$request->user() || !$request->user()->isVendor()) {
                abort(403);
            }
            return $next($request);
        });
    }

    protected function vendorForUser()
    {
        return Vendor::where('user_id', Auth::id())->first();
    }

    public function index()
    {
        $vendor = $this->vendorForUser();
        $packages = $vendor ? $vendor->packages : collect();
        return view('vendor.packages.index', compact('packages','vendor'));
    }

    public function create()
    {
        return view('vendor.packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price_min' => 'required|numeric|min:0',
            'price_max' => 'required|numeric|gte:price_min',
            'description' => 'required|string',
            'features' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        $vendor = $this->vendorForUser();
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor profile not found.');
        }

        $slug = Str::slug($request->name) . '-' . uniqid();

        $imagePath = $request->image_url ?? null;
        if ($request->hasFile('image')) {
            $stored = $request->file('image')->store('uploads', 'public');
            $imagePath = Storage::url($stored);
        }

        $pkg = Package::create([
            'name' => $request->name,
            'slug' => $slug,
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'description' => $request->description,
            'features' => json_encode(array_map('trim', explode(',', $request->features ?? ''))),
            'image_path' => $imagePath,
            'vendor_id' => $vendor->id,
        ]);

        return redirect()->route('vendor.packages.index')->with('success', 'Package created.');
    }

    public function edit(Package $package)
    {
        $vendor = $this->vendorForUser();
        if (!$vendor || $package->vendor_id !== $vendor->id) abort(403);
        return view('vendor.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $vendor = $this->vendorForUser();
        if (!$vendor || $package->vendor_id !== $vendor->id) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'price_min' => 'required|numeric|min:0',
            'price_max' => 'required|numeric|gte:price_min',
            'description' => 'required|string',
            'features' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        $imagePath = $request->image_url ?? $package->image_path;
        if ($request->hasFile('image')) {
            $stored = $request->file('image')->store('uploads', 'public');
            $imagePath = Storage::url($stored);
        }

        $package->update([
            'name' => $request->name,
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'description' => $request->description,
            'features' => json_encode(array_map('trim', explode(',', $request->features ?? ''))),
            'image_path' => $imagePath,
        ]);

        return redirect()->route('vendor.packages.index')->with('success', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        $vendor = $this->vendorForUser();
        if (!$vendor || $package->vendor_id !== $vendor->id) abort(403);
        $package->delete();
        return redirect()->route('vendor.packages.index')->with('success', 'Package removed.');
    }
}
