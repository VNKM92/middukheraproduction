<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\WebhookLog;
use App\Services\Payment\CashfreeService;
use App\Services\Sms\SmsManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CashfreeWebhookController extends Controller
{
    protected CashfreeService $cashfreeService;

    public function __construct(CashfreeService $cashfreeService)
    {
        $this->cashfreeService = $cashfreeService;
    }

    /**
     * Handle Inbound Webhooks from Cashfree
     * Endpoint: POST /cashfree/webhook
     */
    public function handle(Request $request): JsonResponse
    {
        $rawPayload = $request->getContent();
        $signature = $request->header('x-webhook-signature');
        $timestamp = $request->header('x-webhook-timestamp');

        $isValidSignature = $this->cashfreeService->verifyWebhookSignature(
            rawBody: $rawPayload,
            signature: $signature,
            timestamp: $timestamp
        );

        $payload = json_decode($rawPayload, true) ?: [];
        $eventType = $payload['type'] ?? ($payload['event_type'] ?? 'unknown_cashfree_event');
        $data = $payload['data'] ?? $payload;

        $orderData = $data['order'] ?? [];
        $paymentData = $data['payment'] ?? [];

        $orderId = $orderData['order_id'] ?? ($data['order_id'] ?? null);
        $cfPaymentId = $paymentData['cf_payment_id'] ?? ($data['cf_payment_id'] ?? null);

        // 1. Audit Inbound Webhook Event in Database
        $webhookLog = null;
        try {
            $webhookLog = WebhookLog::create([
                'event_id' => $data['event_id'] ?? ($cfPaymentId ? 'cf_evt_' . $cfPaymentId : ('wh_' . time())),
                'event_type' => 'cashfree.' . $eventType,
                'signature' => $signature,
                'is_valid_signature' => $isValidSignature,
                'processed' => false,
                'status_message' => 'Received from Cashfree PG',
                'payload' => $payload,
                'ip_address' => $request->ip(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log Cashfree webhook: ' . $e->getMessage());
        }

        if (!$isValidSignature && !$this->cashfreeService->isSimulationMode()) {
            if ($webhookLog) {
                $webhookLog->update(['status_message' => 'Signature mismatch - Rejected']);
            }
            return response()->json(['error' => 'Signature mismatch'], 400);
        }

        // 2. Process Payment Events
        $isSuccessEvent = in_array(strtoupper($eventType), [
            'PAYMENT_SUCCESS_WEBHOOK',
            'ORDER_PAID_SUCCESS',
            'PAYMENT_SUCCESS',
        ], true) || (isset($paymentData['payment_status']) && strtoupper($paymentData['payment_status']) === 'SUCCESS');

        if ($isSuccessEvent && !empty($orderId)) {
            $booking = Booking::with('user', 'package')->where('cashfree_order_id', $orderId)->first();
            if ($booking) {
                $alreadyPaid = ($booking->payment_status === 'completed');

                $booking->update([
                    'payment_status' => 'completed',
                    'status' => 'progress',
                    'payment_gateway' => 'cashfree',
                    'cashfree_payment_id' => $cfPaymentId ?: $booking->cashfree_payment_id,
                ]);

                $transaction = Transaction::where('booking_id', $booking->id)->latest()->first();
                if ($transaction) {
                    $transaction->update([
                        'status' => 'captured',
                        'gateway' => 'cashfree',
                        'cashfree_payment_id' => $cfPaymentId ?: $transaction->cashfree_payment_id,
                        'raw_response' => $payload,
                    ]);
                }

                Payment::updateOrCreate(
                    [
                        'booking_id' => $booking->id,
                        'payment_gateway' => 'cashfree',
                        'gateway_payment_id' => $cfPaymentId ?: ('cf_' . time()),
                    ],
                    [
                        'amount' => $paymentData['payment_amount'] ?? $booking->amount,
                        'status' => 'captured',
                        'gateway_order_id' => $orderId,
                        'payment_method' => $paymentData['payment_group'] ?? 'cashfree',
                        'raw_payload' => $payload,
                    ]
                );

                // Dispatch SMS confirmation if not already sent
                if (!$alreadyPaid) {
                    $phone = $booking->customer_phone ?: ($booking->user->phone ?? null);
                    if ($phone) {
                        SmsManager::sendPaymentSuccessSms($phone, [
                            'name' => $booking->user->name ?? 'Valued Client',
                            'amount' => $booking->amount,
                            'booking_id' => $booking->id,
                            'package' => $booking->package->name ?? 'Photoshoot',
                            'payment_id' => $cfPaymentId ?: 'Cashfree',
                            'gateway' => 'Cashfree',
                        ]);
                    }

                    SmsManager::sendAdminAlertSms([
                        'name' => $booking->user->name ?? 'Valued Client',
                        'amount' => $booking->amount,
                        'booking_id' => $booking->id,
                        'package' => $booking->package->name ?? 'Photoshoot',
                        'gateway' => 'Cashfree',
                    ]);
                }

                if ($webhookLog) {
                    $webhookLog->update([
                        'processed' => true,
                        'status_message' => 'Processed successfully. Booking #' . $booking->id . ' confirmed.',
                    ]);
                }
            }
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
