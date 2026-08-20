<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Msg91Driver implements SmsGatewayInterface
{
    protected ?string $authKey;
    protected ?string $senderId;
    protected ?string $dltTemplateId;

    public function __construct(?string $authKey = null, ?string $senderId = null, ?string $dltTemplateId = null)
    {
        $this->authKey = $authKey;
        $this->senderId = $senderId;
        $this->dltTemplateId = $dltTemplateId;
    }

    public function send(string $to, string $message, array $extra = []): array
    {
        if (empty($this->authKey)) {
            return [
                'success' => false,
                'message' => 'MSG91 Auth Key not configured.',
                'response' => null,
            ];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $to);
        if (strlen($cleanPhone) === 10) {
            $cleanPhone = '91' . $cleanPhone;
        }

        try {
            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'Content-Type' => 'application/json',
            ])->post('https://control.msg91.com/api/v5/flow/', [
                'template_id' => $extra['template_id'] ?? $this->dltTemplateId,
                'sender' => $this->senderId ?? 'LUMINA',
                'short_url' => '0',
                'recipients' => [
                    [
                        'mobiles' => $cleanPhone,
                        'message' => $message,
                    ]
                ],
            ]);

            $json = $response->json();

            if ($response->successful() && ($json['type'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'message' => $json['message'] ?? 'SMS dispatched successfully via MSG91.',
                    'response' => $json,
                ];
            }

            return [
                'success' => false,
                'message' => $json['message'] ?? 'MSG91 dispatch failed.',
                'response' => $json,
            ];
        } catch (\Exception $e) {
            Log::error('MSG91 Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'MSG91 Error: ' . $e->getMessage(),
                'response' => null,
            ];
        }
    }
}
