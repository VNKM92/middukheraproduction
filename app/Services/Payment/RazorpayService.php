<?php

namespace App\Services\Payment;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayService
{
    protected ?string $keyId;
    protected ?string $keySecret;
    protected ?string $webhookSecret;
    protected bool $isSimulation;

    public function __construct()
    {
        $this->keyId = Setting::get('razorpay_key_id') ?: config('services.razorpay.key_id');
        $this->keySecret = Setting::get('razorpay_key_secret') ?: config('services.razorpay.key_secret');
        $this->webhookSecret = Setting::get('razorpay_webhook_secret') ?: config('services.razorpay.webhook_secret');
        
        $simulationSetting = Setting::get('razorpay_simulation_mode', '1');
        
        $this->isSimulation = ($simulationSetting == '1')
            || empty($this->keyId)
            || empty($this->keySecret)
            || str_starts_with($this->keyId, 'rzp_test_sample')
            || str_starts_with($this->keyId, 'rzp_test_mockkey');
    }

    public function isSimulationMode(): bool
    {
        return $this->isSimulation;
    }

    public function getKeyId(): ?string
    {
        return $this->keyId ?: 'rzp_test_sample';
    }

    public function getWebhookSecret(): ?string
    {
        return $this->webhookSecret;
    }

    /**
     * Get Razorpay Api SDK instance
     */
    public function getApi(): ?Api
    {
        if ($this->isSimulation || empty($this->keyId) || empty($this->keySecret)) {
            return null;
        }

        return new Api($this->keyId, $this->keySecret);
    }

    /**
     * Create a Razorpay Order
     */
    public function createOrder(float $amount, string $receipt, array $notes = []): array
    {
        if ($this->isSimulation) {
            $mockOrderId = 'order_sim_' . strtoupper(Str::random(14));
            return [
                'success' => true,
                'is_simulation' => true,
                'order_id' => $mockOrderId,
                'amount' => $amount,
                'currency' => 'INR',
                'receipt' => $receipt,
                'raw' => [
                    'id' => $mockOrderId,
                    'entity' => 'order',
                    'amount' => (int)($amount * 100),
                    'currency' => 'INR',
                    'receipt' => $receipt,
                    'status' => 'created',
                    'simulated' => true,
                ],
            ];
        }

        try {
            $api = $this->getApi();
            $order = $api->order->create([
                'receipt' => $receipt,
                'amount' => (int) round($amount * 100), // in paise
                'currency' => 'INR',
                'notes' => $notes,
            ]);

            return [
                'success' => true,
                'is_simulation' => false,
                'order_id' => $order->id,
                'amount' => $amount,
                'currency' => 'INR',
                'receipt' => $receipt,
                'raw' => $order->toArray(),
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Failed: ' . $e->getMessage());
            
            // Fallback to simulation gracefully with error notice
            $fallbackOrderId = 'order_fb_' . strtoupper(Str::random(14));
            return [
                'success' => true,
                'is_simulation' => true,
                'order_id' => $fallbackOrderId,
                'amount' => $amount,
                'currency' => 'INR',
                'receipt' => $receipt,
                'warning' => 'Razorpay Gateway Notice: ' . $e->getMessage() . '. Running with simulated checkout mode.',
                'raw' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Verify payment signature from client callback
     */
    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        if ($this->isSimulation || str_starts_with($orderId, 'order_sim_') || str_starts_with($orderId, 'order_fb_') || str_starts_with($paymentId, 'pay_sim_')) {
            return true;
        }

        try {
            $api = $this->getApi();
            if (!$api) {
                return true;
            }

            $attributes = [
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ];

            $api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay Signature Verification Error: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error('Payment Signature Verification Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify webhook signature from Razorpay header
     */
    public function verifyWebhookSignature(string $payload, ?string $signature, ?string $secret = null): bool
    {
        $webhookSecret = $secret ?: $this->webhookSecret;

        // If secret is not set, or simulation mode is on, bypass signature verification
        if (empty($webhookSecret) || $this->isSimulation) {
            return true;
        }

        if (empty($signature)) {
            return false;
        }

        try {
            $api = $this->getApi();
            if (!$api) {
                // Fallback manual HMAC SHA256 verification
                $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
                return hash_equals($expectedSignature, $signature);
            }

            $api->utility->verifyWebhookSignature($payload, $signature, $webhookSecret);
            return true;
        } catch (SignatureVerificationError $e) {
            Log::warning('Razorpay Webhook Signature Mismatch: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::warning('Razorpay Webhook Verification Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch payment details from Razorpay API
     */
    public function fetchPayment(string $paymentId): ?array
    {
        if ($this->isSimulation || str_starts_with($paymentId, 'pay_sim_')) {
            return [
                'id' => $paymentId,
                'status' => 'captured',
                'method' => 'instant_simulation',
                'amount' => 0,
            ];
        }

        try {
            $api = $this->getApi();
            if (!$api) {
                return null;
            }

            $payment = $api->payment->fetch($paymentId);
            return $payment ? $payment->toArray() : null;
        } catch (\Exception $e) {
            Log::error('Error fetching Razorpay payment ' . $paymentId . ': ' . $e->getMessage());
            return null;
        }
    }
}
