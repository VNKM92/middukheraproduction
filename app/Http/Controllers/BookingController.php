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

        // Enforce user authentication before package checkout
        if (!Auth::check()) {
            session(['cart_package_slug' => $slug]);
            return redirect()->route('login')->with('info', 'Please log in to your account or sign up to reserve this package.');
        }

        $meta_title = $package->name . ' — Reserve Luxury Session | ' . (Setting::get('site_name', 'Middukhera Production'));
        $meta_description = Str::limit(strip_tags($package->description), 155);
        $meta_image = $package->image_path;

        return view('booking.checkout', compact('package', 'meta_title', 'meta_description', 'meta_image'));
    }

    public function store(Request $request)
    {
        // Enforce user authentication
        if (!Auth::check()) {
            if ($request->filled('package_id')) {
                $pkg = Package::find($request->package_id);
                if ($pkg) {
                    session(['cart_package_slug' => $pkg->slug]);
                }
            }
            return redirect()->route('login')->with('error', 'Please log in to your account to reserve a package.');
        }

        $user = Auth::user();

        $rules = [
            'package_id' => 'required|exists:packages,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:1000',
            'client_phone' => 'nullable|string|max:20',
            'otp_token' => 'nullable|string',
        ];

        $request->validate($rules);

        // Check if OTP requirement is enforced
        $otpRequired = Setting::get('otp_verification_required', '0') == '1';
        $clientPhone = $request->client_phone;

       

        if ($otpRequired && !empty($clientPhone) && !empty($request->otp_token)) {
            $otpRecord = OtpVerification::where('token', $request->otp_token)->first();
            if (!$otpRecord || $otpRecord->status !== 'verified') {
                return redirect()->back()->withInput()->with('error', 'Phone verification is required. Please verify your phone number via OTP.');
            }
        }

        $package = Package::findOrFail($request->package_id);

            
        // 1. Create Booking record in pending status
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
        try {
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

             
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Could not create Razorpay order: ' . $e->getMessage());
        }
    }

    /**
     * API: Create Razorpay Order
     * Endpoint: POST /api/create-order
     */
    public function createOrderApi(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric', // in INR or paise
            'currency' => 'nullable|string|size:3',
            'receipt' => 'nullable|string|max:40',
            'package_id' => 'nullable|exists:packages,id',
            'booking_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'client_phone' => 'nullable|string',
        ]);

        $currency = $request->input('currency', 'INR');
        $rawAmount = (float) $request->amount;
        
        // If amount passed is in paise (>= 100 paise), convert to rupees if needed or detect
        // Typically API takes amount in paise (minimum 100 paise = 1 INR) or in INR
        $amountInRupees = $rawAmount > 1000 && !isset($request->is_rupees) && $request->has('amount_in_paise')
            ? ($rawAmount / 100)
            : $rawAmount;
            
        // Check minimum 100 paise (Rs. 1.00)
        if (($amountInRupees * 100) < 100) {
            return response()->json([
                'error' => 'Minimum amount is 100 paise (₹1.00).'
            ], 400);
        }

        $receipt = $request->receipt ?: ('rcpt_' . time() . '_' . Str::random(5));
        
        $booking = null;
        $transaction = null;
        $user = Auth::user();

        if ($request->filled('package_id') && $user) {
            $package = Package::find($request->package_id);
            if ($package) {
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'booking_date' => $request->booking_date ?: date('Y-m-d', strtotime('+3 days')),
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'amount' => $amountInRupees,
                    'notes' => $request->notes,
                    'customer_phone' => $request->client_phone,
                ]);

               
                $transactionRef = 'TRX-' . strtoupper(Str::random(10));
                $transaction = Transaction::create([
                    'transaction_ref' => $transactionRef,
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'amount' => $booking->amount,
                    'currency' => $currency,
                    'status' => 'initiated',
                    'customer_name' => $user->name,
                    'customer_email' => $user->email,
                    'customer_phone' => $request->client_phone,
                    'ip_address' => $request->ip(),
                ]);
            }
        }

        try {
            $notes = [
                'receipt' => $receipt,
            ];
            if ($booking) {
                $notes['booking_id'] = (string)$booking->id;
                $notes['transaction_ref'] = $transaction->transaction_ref;
            }

            $orderResult = $this->razorpayService->createOrder(
                amount: $amountInRupees,
                receipt: $receipt,
                notes: $notes,
                currency: $currency
            );

            if ($booking) {
                $booking->update(['razorpay_order_id' => $orderResult['order_id']]);
            }
            if ($transaction) {
                $transaction->update([
                    'razorpay_order_id' => $orderResult['order_id'],
                    'status' => 'processing',
                    'raw_response' => $orderResult['raw'] ?? null,
                ]);
            }

           //  dd($booking);


            return response()->json([
                'success' => true,
                'order_id' => $orderResult['order_id'],
                'amount' => (int) round($amountInRupees * 100), // in paise for frontend SDK
                'currency' => $currency,
                'key_id' => $this->razorpayService->getKeyId(),
                'booking_id' => $booking ? $booking->id : null,
            ], 200);
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            return response()->json(['error' => 'Authentication failed: ' . $e->getMessage()], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Verify Payment Signature
     * Endpoint: POST /api/verify-payment
     */
    public function verifyPaymentApi(Request $request)
    {
        $orderId = $request->razorpay_order_id;
        $paymentId = $request->razorpay_payment_id;
        $signature = $request->razorpay_signature;

        if (empty($orderId) || empty($paymentId) || empty($signature)) {
            return response()->json([
                'success' => false,
                'error' => 'Missing required payment verification parameters: razorpay_order_id, razorpay_payment_id, and razorpay_signature are required.'
            ], 400);
        }

        $isValid = $this->razorpayService->verifyPaymentSignature(
            orderId: $orderId,
            paymentId: $paymentId,
            signature: $signature
        );

        if (!$isValid) {
            // Find booking/transaction if any and record failure
            $booking = Booking::where('razorpay_order_id', $orderId)->first();
            if ($booking) {
                $booking->update(['payment_status' => 'failed']);
            }
            $transaction = Transaction::where('razorpay_order_id', $orderId)->first();
            if ($transaction) {
                $transaction->update([
                    'status' => 'failed',
                    'failure_reason' => 'Razorpay payment signature mismatch or tampering detected.'
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Payment signature verification failed. Mismatch detected.'
            ], 400);
        }

        // On successful verification, update database and send SMS
        $booking = Booking::where('razorpay_order_id', $orderId)->first();
        if (!$booking && $request->filled('booking_id')) {
            $booking = Booking::find($request->booking_id);
        }

        if ($booking) {
            $booking->update([
                'payment_status' => 'completed',
                'status' => 'progress',
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ]);

            Payment::updateOrCreate(
                ['razorpay_payment_id' => $paymentId],
                [
                    'booking_id' => $booking->id,
                    'amount' => $booking->amount,
                    'status' => 'captured',
                    'payment_method' => $request->payment_method ?? 'razorpay',
                    'raw_payload' => [
                        'order_id' => $orderId,
                        'payment_id' => $paymentId,
                        'signature' => $signature,
                    ],
                ]
            );

            $transaction = Transaction::where('booking_id', $booking->id)->latest()->first();
            if ($transaction) {
                $transaction->update([
                    'status' => 'captured',
                    'payment_method' => $request->payment_method ?? 'razorpay',
                    'razorpay_payment_id' => $paymentId,
                    'razorpay_signature' => $signature,
                    'raw_response' => [
                        'order_id' => $orderId,
                        'payment_id' => $paymentId,
                        'verified_at' => now()->toIso8601String(),
                    ],
                ]);
            }

            // Send Confirmation Custom SMS to Contact Person
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
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully and order captured.',
            'redirect_url' => route('client.dashboard'),
        ], 200);
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
