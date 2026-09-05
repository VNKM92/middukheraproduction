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
        $this->sid = $sid ? trim($sid) : null;
        $this->token = $token ? trim($token) : null;
        $this->fromNumber = $fromNumber ? trim($fromNumber) : null;
    }

    public function send(string $to, string $message, array $extra = []): array
    {
        if (empty($this->sid) || empty($this->token) || empty($this->fromNumber)) {
            return [
                'success' => false,
                'message' => 'Twilio SID, Token, or From Number not configured in Admin Settings or .env file.',
                'response' => null,
            ];
        }

        // Standardize recipient phone to E.164 format
        $toClean = trim($to);
        if (str_starts_with($toClean, '+')) {
            $toFormatted = '+' . preg_replace('/[^0-9]/', '', substr($toClean, 1));
        } else {
            $digits = preg_replace('/[^0-9]/', '', $toClean);
            if (strlen($digits) === 10) {
                // Standard 10-digit Indian number
                $toFormatted = '+91' . $digits;
            } elseif (strlen($digits) === 12 && str_starts_with($digits, '91')) {
                $toFormatted = '+' . $digits;
            } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
                $toFormatted = '+91' . substr($digits, 1);
            } else {
                $toFormatted = '+' . $digits;
            }
        }

        $fromNumber = $extra['from'] ?? $this->fromNumber;

        try {
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json";

            $response = Http::withBasicAuth($this->sid, $this->token)
                ->timeout(15)
                ->asForm()
                ->post($url, [
                    'To' => $toFormatted,
                    'From' => $fromNumber,
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

            // Extract descriptive Twilio error
            $errorCode = $json['code'] ?? null;
            $errorMsg = $json['message'] ?? ($response->body() ?: 'Twilio SMS dispatch failed.');
            $errorDetail = $errorCode ? "Twilio Error [{$errorCode}]: {$errorMsg}" : "Twilio Error: {$errorMsg}";

            Log::warning('Twilio Dispatch Failed: ' . $errorDetail, ['response' => $json, 'to' => $toFormatted]);

            return [
                'success' => false,
                'message' => $errorDetail,
                'response' => $json ?: ['body' => $response->body(), 'status' => $response->status()],
            ];
        } catch (\Exception $e) {
            Log::error('Twilio Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Twilio Connection Error: ' . $e->getMessage(),
                'response' => null,
            ];
        }
    }
}
