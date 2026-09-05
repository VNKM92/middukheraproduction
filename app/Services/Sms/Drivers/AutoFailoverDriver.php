<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Log;

class AutoFailoverDriver implements SmsGatewayInterface
{
    protected TwilioDriver $twilio;
    protected Fast2SmsDriver $fast2sms;

    public function __construct(?TwilioDriver $twilio = null, ?Fast2SmsDriver $fast2sms = null)
    {
        $this->twilio = $twilio ?? new TwilioDriver();
        $this->fast2sms = $fast2sms ?? new Fast2SmsDriver();
    }

    public function send(string $to, string $message, array $extra = []): array
    {
        // 1. Primary Attempt: Twilio Gateway
        $twilioResult = $this->twilio->send($to, $message, $extra);

        if (!empty($twilioResult['success'])) {
            return array_merge($twilioResult, [
                'driver_used' => 'twilio',
            ]);
        }

        $twilioReason = $twilioResult['message'] ?? 'Twilio dispatch failed.';
        Log::info("SMS Failover Triggered: Twilio primary failed ({$twilioReason}). Attempting Fast2SMS secondary gateway...", [
            'to' => $to,
        ]);

        // 2. Secondary Fallback Attempt: Fast2SMS Gateway
        $fast2smsResult = $this->fast2sms->send($to, $message, $extra);

        if (!empty($fast2smsResult['success'])) {
            return [
                'success' => true,
                'message' => 'SMS sent via Fast2SMS (Twilio failover: ' . $twilioReason . ')',
                'response' => $fast2smsResult['response'] ?? null,
                'driver_used' => 'fast2sms (fallback)',
            ];
        }

        $fast2smsReason = $fast2smsResult['message'] ?? 'Fast2SMS dispatch failed.';

        // 3. Both Gateways Failed - Return unified actionable error
        return [
            'success' => false,
            'message' => "All SMS Gateways Failed. [1. Twilio: {$twilioReason}] | [2. Fast2SMS: {$fast2smsReason}]",
            'response' => [
                'twilio' => $twilioResult['response'] ?? null,
                'fast2sms' => $fast2smsResult['response'] ?? null,
            ],
            'driver_used' => 'failover_failed',
        ];
    }
}
