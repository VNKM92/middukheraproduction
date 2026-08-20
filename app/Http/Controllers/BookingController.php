<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\OtpVerification;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payment\RazorpayService;
use App\Services\Sms\SmsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    protected RazorpayService $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

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
            'client_phone' => 'nullable|string|max:20',
            'otp_token' => 'nullable|string',
        ];

        // If guest is checking out, validate client contact info
        if (!Auth::check()) {
            $rules['client_name'] = 'required|string|max:255';
            $rules['client_email'] = 'required|email|max:255';
        }

        $request->validate($rules);

        // Check if OTP requirement is enforced
        $otpRequired = Setting::get('otp_verification_required', '1') == '1';
        $clientPhone = $request->client_phone;

        if ($otpRequired && !empty($clientPhone) && !empty($request->otp_token)) {
            $otpRecord = OtpVerification::where('token', $request->otp_token)->first();
            if (!$otpRecord || $otpRecord->status !== 'verified') {
                return redirect()->back()->withInput()->with('error', 'Phone verification is required. Please verify your phone number via OTP.');
            }
        }

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
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'client',
                ]);
            }
            Auth::login($user);
        }

        // 1. Create Booking record
        $booking = Booking::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'booking_date' => $request->booking_date,
            'status' => 'pending',
            'payment_status' => 'pending',
            'amount' => $request->amount,
            'notes' => $request->notes,
            'customer_phone' => $clientPhone,
        ]);

        // 2. Initialize Transaction Tracking record
        $transactionRef = 'TRX-' . strtoupper(Str::random(10));
        $transaction = Transaction::create([
            'transaction_ref' => $transactionRef,
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'amount' => $booking->amount,
            'currency' => 'INR',
            'status' => 'initiated',
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $clientPhone,
            'ip_address' => $request->ip(),
        ]);

        // 3. Create Razorpay Order via RazorpayService
        $orderResult = $this->razorpayService->createOrder(
            amount: (float) $booking->amount,
            receipt: 'rcpt_' . $booking->id,
            notes: [
                'booking_id' => (string)$booking->id,
                'transaction_ref' => $transactionRef,
                'package_name' => $package->name,
                'customer_email' => $user->email,
            ]
        );

        $orderId = $orderResult['order_id'];
        $isSimulation = $orderResult['is_simulation'] ?? false;

        $booking->update(['razorpay_order_id' => $orderId]);
        $transaction->update([
            'razorpay_order_id' => $orderId,
            'status' => 'processing',
            'raw_response' => $orderResult['raw'] ?? null,
        ]);

        return view('booking.payment', [
            'booking' => $booking,
            'package' => $package,
            'transaction' => $transaction,
            'isMock' => $isSimulation,
            'keyId' => $this->razorpayService->getKeyId(),
            'warning' => $orderResult['warning'] ?? null,
        ]);
    }

    public function callback(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::with('user', 'package')->findOrFail($request->booking_id);
        $transaction = Transaction::where('booking_id', $booking->id)->latest()->first();

        // 1. Handle Simulated / Mock Checkout
        if ($request->has('mock_payment') && $request->mock_payment == '1') {
            $paymentId = 'pay_sim_' . strtoupper(Str::random(12));
            $sig = 'sig_sim_' . Str::random(24);

            $booking->update([
                'payment_status' => 'completed',
                'status' => 'progress',
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $sig,
            ]);

            if ($transaction) {
                $transaction->update([
                    'status' => 'captured',
                    'payment_method' => 'instant_simulation',
                    'razorpay_payment_id' => $paymentId,
                    'razorpay_signature' => $sig,
                    'raw_response' => [
                        'simulation' => true,
                        'timestamp' => now()->toIso8601String(),
                        'note' => 'Instant simulated checkout in development mode',
                    ],
                ]);
            }

            Payment::updateOrCreate(
                ['razorpay_payment_id' => $paymentId],
                [
                    'booking_id' => $booking->id,
                    'amount' => $booking->amount,
                    'status' => 'captured',
                    'payment_method' => 'instant_simulation',
                    'raw_payload' => ['simulation' => true, 'timestamp' => now()],
                ]
            );

            // Send Confirmation Custom SMS
            $phone = $booking->customer_phone;
            if ($phone) {
                SmsManager::sendPaymentSuccessSms($phone, [
                    'name' => $booking->user->name ?? 'Valued Client',
                    'amount' => $booking->amount,
                    'booking_id' => $booking->id,
                    'package' => $booking->package->name ?? 'Photoshoot',
                    'payment_id' => $paymentId,
                ]);
            }

            // Send Admin Alert SMS
            SmsManager::sendAdminAlertSms([
                'name' => $booking->user->name ?? 'Valued Client',
                'amount' => $booking->amount,
                'booking_id' => $booking->id,
                'package' => $booking->package->name ?? 'Photoshoot',
            ]);

            return redirect()->route('client.dashboard')->with('success', 'Payment Successful! Your photoshoot session #' . $booking->id . ' is confirmed.');
        }

        // 2. Handle Live Razorpay Signature Verification
        $razorpayPaymentId = $request->razorpay_payment_id;
        $razorpayOrderId = $request->razorpay_order_id;
        $razorpaySignature = $request->razorpay_signature;

        $isValidSignature = $this->razorpayService->verifyPaymentSignature(
            orderId: $razorpayOrderId ?: ($booking->razorpay_order_id ?? ''),
            paymentId: $razorpayPaymentId,
            signature: $razorpaySignature
        );

        if ($isValidSignature) {
            $booking->update([
                'payment_status' => 'completed',
                'status' => 'progress',
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
            ]);

            if ($transaction) {
                $transaction->update([
                    'status' => 'captured',
                    'payment_method' => $request->payment_method ?? 'razorpay',
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'razorpay_signature' => $razorpaySignature,
                    'raw_response' => [
                        'order_id' => $razorpayOrderId,
                        'payment_id' => $razorpayPaymentId,
                        'verified_at' => now()->toIso8601String(),
                    ],
                ]);
            }

            Payment::updateOrCreate(
                ['razorpay_payment_id' => $razorpayPaymentId],
                [
                    'booking_id' => $booking->id,
                    'amount' => $booking->amount,
                    'status' => 'captured',
                    'payment_method' => $request->payment_method ?? 'razorpay',
                    'raw_payload' => [
                        'order_id' => $razorpayOrderId,
                        'payment_id' => $razorpayPaymentId,
                        'signature' => $razorpaySignature,
                    ],
                ]
            );

            // Send Confirmation Custom SMS
            $phone = $booking->customer_phone;
            if ($phone) {
                SmsManager::sendPaymentSuccessSms($phone, [
                    'name' => $booking->user->name ?? 'Valued Client',
                    'amount' => $booking->amount,
                    'booking_id' => $booking->id,
                    'package' => $booking->package->name ?? 'Photoshoot',
                    'payment_id' => $razorpayPaymentId,
                ]);
            }

            // Send Admin Alert SMS
            SmsManager::sendAdminAlertSms([
                'name' => $booking->user->name ?? 'Valued Client',
                'amount' => $booking->amount,
                'booking_id' => $booking->id,
                'package' => $booking->package->name ?? 'Photoshoot',
            ]);

            return redirect()->route('client.dashboard')->with('success', 'Razorpay Payment Captured! Your photoshoot studio booking is confirmed.');
        }

        // 3. Signature verification failed
        $booking->update(['payment_status' => 'failed']);
        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'failure_reason' => 'Razorpay payment signature mismatch or tampering detected.',
            ]);
        }

        if ($booking->customer_phone) {
            SmsManager::sendPaymentFailedSms($booking->customer_phone, [
                'name' => $booking->user->name ?? 'Valued Client',
                'amount' => $booking->amount,
                'booking_id' => $booking->id,
                'reason' => 'Signature verification failed',
                'retry_url' => route('booking.checkout', $booking->package->slug ?? 'package'),
            ]);
        }

        return redirect()->route('client.dashboard')->with('error', 'Payment Signature Verification Failed. If money was debited, it will be refunded or updated automatically via Webhook.');
    }
}
