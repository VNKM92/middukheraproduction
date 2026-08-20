<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $keyId = config('services.razorpay.key_id');
        $keySecret = config('services.razorpay.key_secret');
        $webhookSecret = config('services.razorpay.webhook_secret');

        // Bypassing webhook verification if mock mode is on or secret is not set
        $isMock = empty($keyId) || str_starts_with($keyId, 'rzp_test_mockkey');
        
        $payload = json_decode($request->getContent(), true);
        
        if (!$payload) {
            return response()->json(['status' => 'invalid payload'], 400);
        }

        if (!$isMock && !empty($webhookSecret)) {
            try {
                $api = new Api($keyId, $keySecret);
                $api->utility->verifyWebhookSignature(
                    $request->getContent(),
                    $request->header('X-Razorpay-Signature'),
                    $webhookSecret
                );
            } catch (SignatureVerificationError $e) {
                return response()->json(['status' => 'signature verification failed'], 400);
            }
        }

        $event = $payload['event'] ?? '';

        if ($event === 'payment.captured' || $event === 'order.paid') {
            $paymentEntity = $payload['payload']['payment']['entity'] ?? null;
            if ($paymentEntity) {
                $orderId = $paymentEntity['order_id'] ?? null;
                $paymentId = $paymentEntity['id'] ?? null;
                $amount = ($paymentEntity['amount'] ?? 0) / 100;
                $method = $paymentEntity['method'] ?? 'razorpay';

                if ($orderId) {
                    $booking = Booking::where('razorpay_order_id', $orderId)->first();
                    if ($booking && $booking->payment_status !== 'completed') {
                        $booking->update([
                            'payment_status' => 'completed',
                            'status' => 'progress',
                            'razorpay_payment_id' => $paymentId,
                        ]);

                        Payment::updateOrCreate(
                            ['razorpay_payment_id' => $paymentId],
                            [
                                'booking_id' => $booking->id,
                                'amount' => $amount,
                                'status' => 'captured',
                                'payment_method' => $method,
                                'raw_payload' => json_encode($payload),
                            ]
                        );
                    }
                }
            }
        } elseif ($event === 'payment.failed') {
            $paymentEntity = $payload['payload']['payment']['entity'] ?? null;
            if ($paymentEntity) {
                $orderId = $paymentEntity['order_id'] ?? null;
                if ($orderId) {
                    $booking = Booking::where('razorpay_order_id', $orderId)->first();
                    if ($booking && $booking->payment_status === 'pending') {
                        $booking->update([
                            'payment_status' => 'failed',
                        ]);
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
