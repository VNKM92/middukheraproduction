<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Fast2SmsDriver implements SmsGatewayInterface
{
    protected ?string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    public function send(string $to, string $message, array $extra = []): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'Fast2SMS API key not configured.',
                'response' => null,
            ];
        }

        // Clean phone number (extract digits, max 10 for India)
        $cleanPhone = preg_replace('/[^0-9]/', '', $to);
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        try {
            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://www.fast2sms.com/dev/bulkV2', [
                'route' => 'q',
                'message' => $message,
                'language' => 'english',
                'flash' => 0,
                'numbers' => $cleanPhone,
            ]);

            $json = $response->json();

            if ($response->successful() && isset($json['return']) && $json['return'] === true) {
                return [
                    'success' => true,
                    'message' => $json['message'][0] ?? 'SMS dispatched successfully via Fast2SMS.',
                    'response' => $json,
                ];
            }

            return [
                'success' => false,
                'message' => $json['message'] ?? 'Fast2SMS dispatch failed.',
                'response' => $json,
            ];
        } catch (\Exception $e) {
            Log::error('Fast2SMS Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Fast2SMS Error: ' . $e->getMessage(),
                'response' => null,
            ];
        }
    }
}
