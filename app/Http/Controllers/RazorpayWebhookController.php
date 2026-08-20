<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\WebhookLog;
use App\Services\Payment\RazorpayService;
use App\Services\Sms\SmsManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    protected RazorpayService $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function handle(Request $request): JsonResponse
    {
        $rawPayload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature') ?? $request->header('x-razorpay-signature');

        $payload = json_decode($rawPayload, true);

        if (!$payload || !is_array($payload)) {
            WebhookLog::create([
                'event_type' => 'invalid_json',
                'signature' => $signature,
                'is_valid_signature' => false,
                'processed' => false,
                'status_message' => 'Malformed or empty JSON payload',
                'ip_address' => $request->ip(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'Invalid JSON payload'], 400);
        }

        $eventId = $payload['event_id'] ?? ($payload['id'] ?? null);
        $event = $payload['event'] ?? 'unknown_event';

        // 1. Signature Verification
        $isValidSignature = $this->razorpayService->verifyWebhookSignature($rawPayload, $signature);

        if (!$isValidSignature) {
            WebhookLog::create([
                'event_id' => $eventId,
                'event_type' => $event,
                'signature' => $signature,
                'is_valid_signature' => false,
                'processed' => false,
                'status_message' => 'Signature verification failed',
                'payload' => $payload,
                'ip_address' => $request->ip(),
            ]);

            Log::warning("Razorpay Webhook: Signature verification failed for event {$event}");
            return response()->json(['status' => 'error', 'message' => 'Webhook signature verification failed'], 400);
        }

        // 2. Check Idempotency (has this exact eventId already been processed?)
        if ($eventId) {
            $alreadyProcessed = WebhookLog::where('event_id', $eventId)->where('processed', true)->exists();
            if ($alreadyProcessed) {
                return response()->json(['status' => 'success', 'message' => 'Event already processed (Idempotent)']);
            }
        }

        $statusMessage = 'Processed successfully';
        $processed = true;

        try {
            // 3. Process Events
            match ($event) {
                'payment.captured', 'order.paid' => $this->handlePaymentCaptured($payload),
                'payment.failed' => $this->handlePaymentFailed($payload),
                'payment.authorized' => $this->handlePaymentAuthorized($payload),
                'refund.created', 'refund.processed' => $this->handleRefund($payload),
                default => $statusMessage = "Event {$event} acknowledged without action",
            };
        } catch (\Exception $e) {
            Log::error("Webhook Event Processing Error ({$event}): " . $e->getMessage());
            $statusMessage = 'Error during event processing: ' . $e->getMessage();
            $processed = false;
        }

        // 4. Save Webhook Audit Log
        WebhookLog::create([
            'event_id' => $eventId,
            'event_type' => $event,
            'signature' => $signature,
            'is_valid_signature' => true,
            'processed' => $processed,
            'status_message' => $statusMessage,
            'payload' => $payload,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $statusMessage,
        ]);
    }

    /**
     * Handle payment.captured / order.paid
     */
    protected function handlePaymentCaptured(array $payload): void
    {
        $paymentEntity = $payload['payload']['payment']['entity'] ?? null;
        if (!$paymentEntity) {
            return;
        }

        $orderId = $paymentEntity['order_id'] ?? null;
        $paymentId = $paymentEntity['id'] ?? null;
        $amount = ($paymentEntity['amount'] ?? 0) / 100; // convert paise to INR
        $method = $paymentEntity['method'] ?? 'razorpay';
        $contact = $paymentEntity['contact'] ?? null;

        $booking = null;
        if ($orderId) {
            $booking = Booking::with('user', 'package')->where('razorpay_order_id', $orderId)->first();
        }

        if ($booking) {
            $booking->update([
                'payment_status' => 'completed',
                'status' => 'progress',
                'razorpay_payment_id' => $paymentId,
            ]);

            // Update or create Transaction record
            $transaction = Transaction::where('booking_id', $booking->id)->latest()->first();
            if ($transaction) {
                $transaction->update([
                    'status' => 'captured',
                    'razorpay_payment_id' => $paymentId,
                    'payment_method' => $method,
                    'raw_response' => $payload,
                ]);
            } else {
                Transaction::create([
                    'transaction_ref' => 'TRX-WH-' . strtoupper(\Illuminate\Support\Str::random(8)),
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'amount' => $amount ?: $booking->amount,
                    'currency' => 'INR',
                    'status' => 'captured',
                    'payment_method' => $method,
                    'razorpay_order_id' => $orderId,
                    'razorpay_payment_id' => $paymentId,
                    'customer_name' => $booking->user->name ?? 'Client',
                    'customer_email' => $booking->user->email ?? null,
                    'customer_phone' => $booking->customer_phone ?? $contact,
                    'raw_response' => $payload,
                ]);
            }

            Payment::updateOrCreate(
                ['razorpay_payment_id' => $paymentId],
                [
                    'booking_id' => $booking->id,
                    'amount' => $amount ?: $booking->amount,
                    'status' => 'captured',
                    'payment_method' => $method,
                    'raw_payload' => $payload,
                ]
            );

            // Send Confirmation SMS
            $phone = $booking->customer_phone ?: $contact;
            if ($phone) {
                SmsManager::sendPaymentSuccessSms($phone, [
                    'name' => $booking->user->name ?? 'Valued Client',
                    'amount' => $booking->amount,
                    'booking_id' => $booking->id,
                    'package' => $booking->package->name ?? 'Photoshoot',
                    'payment_id' => $paymentId,
                ]);
            }
        }
    }

    /**
     * Handle payment.failed
     */
    protected function handlePaymentFailed(array $payload): void
    {
        $paymentEntity = $payload['payload']['payment']['entity'] ?? null;
        if (!$paymentEntity) {
            return;
        }

        $orderId = $paymentEntity['order_id'] ?? null;
        $paymentId = $paymentEntity['id'] ?? null;
        $errorCode = $paymentEntity['error_code'] ?? 'PAYMENT_FAILED';
        $errorDesc = $paymentEntity['error_description'] ?? 'Payment failed on gateway';
        $contact = $paymentEntity['contact'] ?? null;

        $booking = null;
        if ($orderId) {
            $booking = Booking::with('user', 'package')->where('razorpay_order_id', $orderId)->first();
        }

        if ($booking) {
            $booking->update([
                'payment_status' => 'failed',
                'razorpay_payment_id' => $paymentId,
            ]);

            $transaction = Transaction::where('booking_id', $booking->id)->latest()->first();
            if ($transaction) {
                $transaction->update([
                    'status' => 'failed',
                    'razorpay_payment_id' => $paymentId,
                    'failure_reason' => "[{$errorCode}] {$errorDesc}",
                    'raw_response' => $payload,
                ]);
            }

            $phone = $booking->customer_phone ?: $contact;
            if ($phone) {
                SmsManager::sendPaymentFailedSms($phone, [
                    'name' => $booking->user->name ?? 'Valued Client',
                    'amount' => $booking->amount,
                    'booking_id' => $booking->id,
                    'reason' => $errorDesc,
                    'retry_url' => route('booking.checkout', $booking->package->slug ?? 'package'),
                ]);
            }
        }
    }

    /**
     * Handle payment.authorized
     */
    protected function handlePaymentAuthorized(array $payload): void
    {
        $paymentEntity = $payload['payload']['payment']['entity'] ?? null;
        if (!$paymentEntity) {
            return;
        }

        $orderId = $paymentEntity['order_id'] ?? null;
        $paymentId = $paymentEntity['id'] ?? null;

        if ($orderId) {
            $transaction = Transaction::where('razorpay_order_id', $orderId)->first();
            if ($transaction && $transaction->status !== 'captured') {
                $transaction->update([
                    'status' => 'processing',
                    'razorpay_payment_id' => $paymentId,
                ]);
            }
        }
    }

    /**
     * Handle refund.created / refund.processed
     */
    protected function handleRefund(array $payload): void
    {
        $refundEntity = $payload['payload']['refund']['entity'] ?? null;
        if (!$refundEntity) {
            return;
        }

        $paymentId = $refundEntity['payment_id'] ?? null;
        if ($paymentId) {
            $transaction = Transaction::where('razorpay_payment_id', $paymentId)->first();
            if ($transaction) {
                $transaction->update([
                    'status' => 'refunded',
                    'raw_response' => array_merge($transaction->raw_response ?? [], ['refund' => $refundEntity]),
                ]);

                if ($transaction->booking) {
                    $transaction->booking->update(['payment_status' => 'refunded']);
                }
            }
        }
    }
}
