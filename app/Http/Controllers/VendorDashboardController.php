<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorDashboardController extends Controller
{
    public function __construct()
    {
        // $this->middleware(function ($request, $next) {
        //     if (!$request->user() || !$request->user()->isVendor()) {
        //         abort(403, 'Unauthorized access to Vendor Dashboard.');
        //     }
        //     return $next($request);
        // });
    }

    public function index()
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        $packages = $vendor ? $vendor->packages : collect();

        $bookings = Booking::whereHas('package', function ($q) use ($vendor) {
            $q->where('vendor_id', $vendor->id ?? 0);
        })->with('user','package')->latest()->get();

        $totalEarnings = $bookings->where('payment_status','completed')->sum('amount');

        return view('vendor.dashboard', compact('vendor','packages','bookings','totalEarnings'));
    }
}
