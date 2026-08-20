<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioDriver implements SmsGatewayInterface
{
    protected ?string $sid;
    protected ?string $token;
    protected ?string $fromNumber;

    public function __construct(?string $sid = null, ?string $token = null, ?string $fromNumber = null)
    {
        $this->sid = $sid;
        $this->token = $token;
        $this->fromNumber = $fromNumber;
    }

    public function send(string $to, string $message, array $extra = []): array
    {
        if (empty($this->sid) || empty($this->token) || empty($this->fromNumber)) {
            return [
                'success' => false,
                'message' => 'Twilio SID, Token, or From Number not configured.',
                'response' => null,
            ];
        }

        // Standardize E.164
        $toFormatted = trim($to);
        if (!str_starts_with($toFormatted, '+')) {
            $toFormatted = '+91' . preg_replace('/[^0-9]/', '', $toFormatted);
        }

        try {
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json";

            $response = Http::withBasicAuth($this->sid, $this->token)
                ->asForm()
                ->post($url, [
                    'To' => $toFormatted,
                    'From' => $this->fromNumber,
                    'Body' => $message,
                ]);

            $json = $response->json();

            if ($response->successful() && !empty($json['sid'])) {
                return [
                    'success' => true,
                    'message' => 'SMS queued successfully with Twilio. SID: ' . $json['sid'],
                    'response' => $json,
                ];
            }

            return [
                'success' => false,
                'message' => $json['message'] ?? 'Twilio SMS dispatch failed.',
                'response' => $json,
            ];
        } catch (\Exception $e) {
            Log::error('Twilio Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Twilio Error: ' . $e->getMessage(),
                'response' => null,
            ];
        }
    }
}
