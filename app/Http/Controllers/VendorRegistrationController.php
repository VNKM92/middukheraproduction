<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class VendorRegistrationController extends Controller
{
    public function show()
    {
        return view('vendor.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'studio_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Create user as 'client' initially. Admin will promote to 'vendor' upon approval.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'client',
        ]);

        $vendor = Vendor::create([
            'user_id' => $user->id,
            'name' => $request->studio_name,
            'slug' => \Illuminate\Support\Str::slug($request->studio_name) . '-' . uniqid(),
            'description' => $request->description,
            'status' => 'pending',
        ]);

        // Notify admin(s) via simple mail if configured
        try {
            $adminEmail = config('mail.from.address');
            if ($adminEmail) {
                Mail::raw("New vendor registration: $vendor->name (user: $user->email). Approve at /admin/vendors", function ($m) use ($adminEmail) {
                    $m->to($adminEmail)->subject('New Vendor Registration');
                });
            }
        } catch (\Exception $e) {
            // ignore mail failures
        }

        return redirect()->route('home')->with('success', 'Vendor registration submitted. You will be notified when approved.');
    }
}
