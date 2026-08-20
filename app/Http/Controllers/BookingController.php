<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class BookingController extends Controller
{
    public function checkout($slug)
    {
        $package = Package::where('slug', $slug)->firstOrFail();

        $meta_title = $package->name . ' — Reserve Luxury Session | ' . (Setting::get('site_name', 'Lumina Studio'));
        $meta_description = Str::limit(strip_tags($package->description), 155);
        $meta_image = $package->image_path;

        return view('booking.checkout', compact('package', 'meta_title', 'meta_description', 'meta_image'));
    }

    public function store(Request $request)
    {
        $rules = [
            'package_id' => 'required|exists:packages,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:1000',
        ];

        // If guest is checking out, validate client contact info
        if (!Auth::check()) {
            $rules['client_name'] = 'required|string|max:255';
            $rules['client_email'] = 'required|email|max:255';
            $rules['client_phone'] = 'nullable|string|max:20';
        }

        $request->validate($rules);

        $package = Package::findOrFail($request->package_id);

        // Handle or create user
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('email', $request->client_email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $request->client_name,
                    'email' => $request->client_email,
                    'password' => Hash::make('Secret123!'),
                    'role' => 'client',
                ]);
            }
            Auth::login($user);
        }

        $booking = Booking::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'booking_date' => $request->booking_date,
            'status' => 'pending',
            'payment_status' => 'pending',
            'amount' => $request->amount,
            'notes' => $request->notes,
        ]);

        $keyId = Setting::get('razorpay_key_id') ?: config('services.razorpay.key_id');
        $keySecret = Setting::get('razorpay_key_secret') ?: config('services.razorpay.key_secret');
        $simulationMode = Setting::get('razorpay_simulation_mode', '1');

        $isMock = empty($keyId) || empty($keySecret) || str_starts_with($keyId, 'rzp_test_sample') || $simulationMode == '1';

        if ($isMock) {
            $booking->update([
                'razorpay_order_id' => 'order_mock_' . uniqid(),
            ]);
            return view('booking.payment', [
                'booking' => $booking,
                'package' => $package,
                'isMock' => true,
                'keyId' => $keyId ?: 'mock_key',
            ]);
        }

        try {
            $api = new Api($keyId, $keySecret);
            $order = $api->order->create([
                'receipt' => 'booking_rcpt_' . $booking->id,
                'amount' => (int)($booking->amount * 100), // in paise
                'currency' => 'INR',
            ]);

            $booking->update([
                'razorpay_order_id' => $order->id,
            ]);

            return view('booking.payment', [
                'booking' => $booking,
                'package' => $package,
                'isMock' => false,
                'keyId' => $keyId,
            ]);
        } catch (\Exception $e) {
            $booking->update([
                'razorpay_order_id' => 'order_fallback_' . uniqid(),
            ]);
            return view('booking.payment', [
                'booking' => $booking,
                'package' => $package,
                'isMock' => true,
                'keyId' => 'fallback_key',
                'warning' => 'Razorpay Gateway Notice: ' . $e->getMessage() . '. Running with simulated checkout mode.',
            ]);
        }
    }

    public function callback(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::with('user', 'package')->findOrFail($request->booking_id);

        if ($request->has('mock_payment') && $request->mock_payment == '1') {
            $paymentId = 'pay_sim_' . strtoupper(Str::random(12));
            $booking->update([
                'payment_status' => 'completed',
                'status' => 'progress',
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => 'sig_sim_' . uniqid(),
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'razorpay_payment_id' => $paymentId,
                'amount' => $booking->amount,
                'status' => 'captured',
                'payment_method' => 'instant_simulation',
                'raw_payload' => json_encode(['simulation' => true, 'timestamp' => now()]),
            ]);

            return redirect()->route('client.dashboard')->with('success', 'Payment Successful! Your photoshoot session #' . $booking->id . ' is confirmed.');
        }

        $razorpayPaymentId = $request->razorpay_payment_id;
        $razorpayOrderId = $request->razorpay_order_id;
        $razorpaySignature = $request->razorpay_signature;

        $keyId = Setting::get('razorpay_key_id') ?: config('services.razorpay.key_id');
        $keySecret = Setting::get('razorpay_key_secret') ?: config('services.razorpay.key_secret');

        try {
            $api = new Api($keyId, $keySecret);
            $attributes = [
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
            ];

            $api->utility->verifyPaymentSignature($attributes);

            $booking->update([
                'payment_status' => 'completed',
                'status' => 'progress',
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'razorpay_payment_id' => $razorpayPaymentId,
                'amount' => $booking->amount,
                'status' => 'captured',
                'payment_method' => $request->payment_method ?? 'razorpay',
                'raw_payload' => json_encode($attributes),
            ]);

            return redirect()->route('client.dashboard')->with('success', 'Razorpay Payment Captured! Your photoshoot studio booking is confirmed.');
        } catch (\Exception $e) {
            $booking->update([
                'payment_status' => 'failed',
            ]);

            return redirect()->route('client.dashboard')->with('error', 'Payment Signature Verification Failed: ' . $e->getMessage());
        }
    }
}
