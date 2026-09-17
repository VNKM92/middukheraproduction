<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\Payment\CashfreeService;
use App\Services\Sms\SmsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CashfreeController extends Controller
{
    protected CashfreeService $cashfreeService;

    public function __construct(CashfreeService $cashfreeService)
    {
        $this->cashfreeService = $cashfreeService;
    }

    /**
     * Handle Customer Return Redirect from Cashfree PG
     * Endpoint: GET /cashfree/return
     */
    public function return(Request $request)
    {
        $orderId = $request->query('order_id') ?: $request->input('order_id');
        $bookingId = $request->query('booking_id') ?: $request->input('booking_id');

        if (empty($orderId) && empty($bookingId)) {
            return redirect()->route('client.dashboard')->with('error', 'Invalid payment return parameters.');
        }

        // Find Booking & Transaction
        $booking = null;
        if (!empty($orderId)) {
            $booking = Booking::with('user', 'package')
                ->where('cashfree_order_id', $orderId)
                ->orWhere('razorpay_order_id', $orderId)
                ->first();
        }
        if (!$booking && !empty($bookingId)) {
            $booking = Booking::with('user', 'package')->find($bookingId);
        }

        if (!$booking) {
            return redirect()->route('client.dashboard')->with('error', 'Booking matching this payment order could not be located.');
        }

        $transaction = Transaction::where('booking_id', $booking->id)->latest()->first();

        // 1. Handle Simulated / Sandbox Checkout
        if ($request->has('mock_payment') && $request->mock_payment == '1') {
            $cfPaymentId = 'cf_pay_sim_' . strtoupper(Str::random(12));

            $booking->update([
                'payment_status' => 'completed',
                'status' => 'progress',
                'payment_gateway' => 'cashfree',
                'cashfree_payment_id' => $cfPaymentId,
            ]);

            if ($transaction) {
                $transaction->update([
                    'status' => 'captured',
                    'gateway' => 'cashfree',
                    'payment_method' => 'cashfree_simulation',
                    'cashfree_payment_id' => $cfPaymentId,
                    'raw_response' => [
                        'simulation' => true,
                        'gateway' => 'cashfree',
                        'timestamp' => now()->toIso8601String(),
                        'note' => 'Instant simulated Cashfree checkout in development/test mode',
                    ],
                ]);
            }

            Payment::updateOrCreate(
                [
                    'booking_id' => $booking->id,
                    'payment_gateway' => 'cashfree',
                    'gateway_payment_id' => $cfPaymentId,
                ],
                [
                    'amount' => $booking->amount,
                    'status' => 'captured',
                    'gateway_order_id' => $orderId ?: ($booking->cashfree_order_id ?? ('cf_' . $booking->id)),
                    'razorpay_payment_id' => null,
                    'payment_method' => 'cashfree_simulation',
                    'raw_payload' => ['simulation' => true, 'timestamp' => now()],
                ]
            );

            // Send Custom SMS via Fast2SMS
            $phone = $booking->customer_phone ?: ($booking->user->phone ?? ($transaction->customer_phone ?? null));
            if ($phone) {
                $smsResult = SmsManager::sendPaymentSuccessSms($phone, [
                    'name' => $booking->user->name ?? 'Valued Client',
                    'amount' => $booking->amount,
                    'booking_id' => $booking->id,
                    'package' => $booking->package->name ?? 'Photoshoot',
                    'package_name' => $booking->package->name ?? 'Photoshoot',
                    'payment_id' => $cfPaymentId,
                    'gateway' => 'Cashfree',
                ]);

                if ($transaction && !empty($smsResult['sent_message'])) {
                    $raw = is_array($transaction->raw_response) ? $transaction->raw_response : (json_decode($transaction->raw_response, true) ?? []);
                    $raw['confirmation_sms'] = [
                        'phone' => $phone,
                        'message' => $smsResult['sent_message'],
                        'status' => $smsResult['success'] ? 'sent' : 'failed',
                        'driver' => $smsResult['driver_used'] ?? null,
                        'sent_at' => now()->toIso8601String(),
                    ];
                    $transaction->update(['raw_response' => $raw]);
                }
            }

            // Send Admin Alert SMS
            SmsManager::sendAdminAlertSms([
                'name' => $booking->user->name ?? 'Valued Client',
                'amount' => $booking->amount,
                'booking_id' => $booking->id,
                'package' => $booking->package->name ?? 'Photoshoot',
                'gateway' => 'Cashfree',
            ]);

            return redirect()->route('client.dashboard')->with('success', 'Cashfree Payment Successful! Your photoshoot session #' . $booking->id . ' is confirmed.');
        }

        // 2. Fetch and Verify Order from Cashfree API
        $orderInfo = $this->cashfreeService->fetchOrder($orderId);
        $payments = $this->cashfreeService->fetchPayments($orderId);

        $orderStatus = strtoupper($orderInfo['order_status'] ?? '');
        $isPaid = $orderStatus === 'PAID';
        $successfulPayment = null;

        if (!$isPaid && !empty($payments)) {
            foreach ($payments as $p) {
                if (strtoupper($p['payment_status'] ?? '') === 'SUCCESS') {
                    $isPaid = true;
                    $successfulPayment = $p;
                    break;
                }
            }
        } elseif ($isPaid && !empty($payments)) {
            $successfulPayment = $payments[0] ?? null;
        }

        if ($isPaid) {
            $cfPaymentId = $successfulPayment['cf_payment_id'] ?? ('cf_pay_' . strtoupper(Str::random(10)));
            $paymentMethod = is_array($successfulPayment['payment_method'] ?? null)
                ? (array_key_first($successfulPayment['payment_method']) ?? 'cashfree')
                : ($successfulPayment['payment_group'] ?? 'cashfree');

            $booking->update([
                'payment_status' => 'completed',
                'status' => 'progress',
                'payment_gateway' => 'cashfree',
                'cashfree_payment_id' => $cfPaymentId,
            ]);

            if ($transaction) {
                $transaction->update([
                    'status' => 'captured',
                    'gateway' => 'cashfree',
                    'payment_method' => $paymentMethod,
                    'cashfree_payment_id' => $cfPaymentId,
                    'raw_response' => [
                        'order' => $orderInfo,
                        'payments' => $payments,
                        'verified_at' => now()->toIso8601String(),
                    ],
                ]);
            }

            Payment::updateOrCreate(
                [
                    'booking_id' => $booking->id,
                    'payment_gateway' => 'cashfree',
                    'gateway_payment_id' => $cfPaymentId,
                ],
                [
                    'amount' => $booking->amount,
                    'status' => 'captured',
                    'gateway_order_id' => $orderId,
                    'payment_method' => $paymentMethod,
                    'raw_payload' => [
                        'order' => $orderInfo,
                        'payment' => $successfulPayment,
                    ],
                ]
            );

            // Send Confirmation Custom SMS to Contact Person
            $phone = $booking->customer_phone ?: ($booking->user->phone ?? ($transaction->customer_phone ?? null));
            if ($phone) {
                $smsResult = SmsManager::sendPaymentSuccessSms($phone, [
                    'name' => $booking->user->name ?? 'Valued Client',
                    'amount' => $booking->amount,
                    'booking_id' => $booking->id,
                    'package' => $booking->package->name ?? 'Photoshoot',
                    'package_name' => $booking->package->name ?? 'Photoshoot',
                    'payment_id' => $cfPaymentId,
                    'gateway' => 'Cashfree',
                ]);

                if ($transaction && !empty($smsResult['sent_message'])) {
                    $raw = is_array($transaction->raw_response) ? $transaction->raw_response : (json_decode($transaction->raw_response, true) ?? []);
                    $raw['confirmation_sms'] = [
                        'phone' => $phone,
                        'message' => $smsResult['sent_message'],
                        'status' => $smsResult['success'] ? 'sent' : 'failed',
                        'driver' => $smsResult['driver_used'] ?? null,
                        'sent_at' => now()->toIso8601String(),
                    ];
                    $transaction->update(['raw_response' => $raw]);
                }
            }

            // Send Admin Alert SMS
            SmsManager::sendAdminAlertSms([
                'name' => $booking->user->name ?? 'Valued Client',
                'amount' => $booking->amount,
                'booking_id' => $booking->id,
                'package' => $booking->package->name ?? 'Photoshoot',
                'gateway' => 'Cashfree',
            ]);

            return redirect()->route('client.dashboard')->with('success', 'Cashfree Payment Captured! Your photoshoot session booking #' . $booking->id . ' is confirmed.');
        }

        // Handle Payment Failure
        $booking->update(['payment_status' => 'failed']);
        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'failure_reason' => 'Cashfree payment not completed or status is ' . ($orderStatus ?: 'PENDING'),
            ]);
        }

        if ($booking->customer_phone) {
            SmsManager::sendPaymentFailedSms($booking->customer_phone, [
                'name' => $booking->user->name ?? 'Valued Client',
                'amount' => $booking->amount,
                'booking_id' => $booking->id,
                'reason' => 'Cashfree payment status: ' . ($orderStatus ?: 'Failed'),
                'retry_url' => route('booking.checkout', $booking->package->slug ?? 'package'),
            ]);
        }

        return redirect()->route('client.dashboard')->with('error', 'Cashfree payment was not completed. If amount was debited, it will be automatically confirmed via webhook or refunded.');
    }

    /**
     * API: Verify Cashfree Payment status via AJAX from front-end SDK callback
     * Endpoint: POST /api/cashfree/verify
     */
    public function verifyApi(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        $orderId = $request->order_id;
        $orderInfo = $this->cashfreeService->fetchOrder($orderId);
        $payments = $this->cashfreeService->fetchPayments($orderId);

        $orderStatus = strtoupper($orderInfo['order_status'] ?? '');
        $isPaid = $orderStatus === 'PAID';

        $booking = Booking::with('user', 'package')->where('cashfree_order_id', $orderId)->first();
        if (!$booking && $request->filled('booking_id')) {
            $booking = Booking::with('user', 'package')->find($request->booking_id);
        }

        if (!$booking) {
            return response()->json(['success' => false, 'error' => 'Booking not found'], 404);
        }

        if ($isPaid) {
            $cfPaymentId = $payments[0]['cf_payment_id'] ?? ('cf_' . time());

            $booking->update([
                'payment_status' => 'completed',
                'status' => 'progress',
                'payment_gateway' => 'cashfree',
                'cashfree_payment_id' => $cfPaymentId,
            ]);

            $transaction = Transaction::where('booking_id', $booking->id)->latest()->first();
            if ($transaction) {
                $transaction->update([
                    'status' => 'captured',
                    'gateway' => 'cashfree',
                    'cashfree_payment_id' => $cfPaymentId,
                ]);
            }

            // Trigger SMS
            $phone = $booking->customer_phone ?: ($booking->user->phone ?? null);
            if ($phone) {
                SmsManager::sendPaymentSuccessSms($phone, [
                    'name' => $booking->user->name ?? 'Valued Client',
                    'amount' => $booking->amount,
                    'booking_id' => $booking->id,
                    'package' => $booking->package->name ?? 'Photoshoot',
                    'payment_id' => $cfPaymentId,
                    'gateway' => 'Cashfree',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cashfree payment verified successfully.',
                'redirect_url' => route('client.dashboard'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment status is ' . $orderStatus,
        ], 400);
    }
}
