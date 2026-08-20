<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomHttpDriver implements SmsGatewayInterface
{
    protected ?string $url;
    protected string $method;
    protected ?string $headersJson;

    public function __construct(?string $url = null, string $method = 'GET', ?string $headersJson = null)
    {
        $this->url = $url;
        $this->method = strtoupper($method ?: 'GET');
        $this->headersJson = $headersJson;
    }

    public function send(string $to, string $message, array $extra = []): array
    {
        if (empty($this->url)) {
            return [
                'success' => false,
                'message' => 'Custom SMS Gateway URL is not configured.',
                'response' => null,
            ];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $to);
        $encodedMessage = urlencode($message);

        // Replace placeholders in URL
        $targetUrl = str_replace(
            ['{phone}', '{mobile}', '{number}', '{message}', '{text}'],
            [$cleanPhone, $cleanPhone, $cleanPhone, $encodedMessage, $encodedMessage],
            $this->url
        );

        $headers = [];
        if (!empty($this->headersJson)) {
            $parsed = json_decode($this->headersJson, true);
            if (is_array($parsed)) {
                $headers = $parsed;
            }
        }

        try {
            $client = Http::withHeaders($headers)->timeout(10);

            if ($this->method === 'POST') {
                $response = $client->post($targetUrl, [
                    'phone' => $cleanPhone,
                    'message' => $message,
                    'extra' => $extra,
                ]);
            } else {
                $response = $client->get($targetUrl);
            }

            $body = $response->body();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Custom HTTP SMS Gateway dispatched successfully.',
                    'response' => [
                        'status' => $response->status(),
                        'body' => Str_limit($body, 500),
                    ],
                ];
            }

            return [
                'success' => false,
                'message' => 'Custom HTTP SMS Gateway returned HTTP ' . $response->status(),
                'response' => [
                    'status' => $response->status(),
                    'body' => Str_limit($body, 500),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Custom HTTP SMS Gateway Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Custom SMS Gateway Error: ' . $e->getMessage(),
                'response' => null,
            ];
        }
    }
}

function Str_limit(string $value, int $limit = 100): string {
    return mb_strlen($value) > $limit ? mb_substr($value, 0, $limit) . '...' : $value;
}
